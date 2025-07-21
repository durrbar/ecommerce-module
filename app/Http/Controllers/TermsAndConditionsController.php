<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Http\Requests\CreateTermsAndConditionsRequest;
use Modules\Ecommerce\Http\Requests\UpdateTermsAndConditionsRequest;
use Modules\Ecommerce\Http\Resources\TermsConditionResource;
use Modules\Ecommerce\Models\TermsAndConditions;
use Modules\Ecommerce\Repositories\TermsAndConditionsRepository;
use Modules\Role\Enums\Permission;
use Prettus\Validator\Exceptions\ValidatorException;

class TermsAndConditionsController extends CoreController
{
    public $repository;

    public function __construct(TermsAndConditionsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|TermsAndConditions[]
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        // $language = $request->language ?? DEFAULT_LANGUAGE;
        $termsAndConditions = $this->fetchTermsAndConditions($request)->paginate($limit)->withQueryString();
        $data = TermsConditionResource::collection($termsAndConditions)->response()->getData(true);

        return formatAPIResourcePaginate($data);
    }

    public function fetchTermsAndConditions(Request $request)
    {

        try {
            $user = $request->user();
            $language = $request->language ?? DEFAULT_LANGUAGE;

            // if statement is for role base authorized scenerio
            // else statment is for global viewers level guest scenerio

            if (isset($user)) {
                switch ($user) {
                    case $user->hasPermissionTo(Permission::SUPER_ADMIN):
                        return $this->repository->with('shop')->where('language', $language);
                        break;

                    case $user->hasPermissionTo(Permission::STORE_OWNER):
                        if ($this->repository->hasPermission($user, $request->shop_id)) {
                            return $this->repository->with('shop')->where('shop_id', '=', $request->shop_id)->where('language', $language);
                        } else {
                            return $this->repository->with('shop')->where('user_id', '=', $user->id)->where('language', $language)->whereIn('shop_id', $user->shops->pluck('id'));
                        }
                        break;

                    case $user->hasPermissionTo(Permission::STAFF):
                        if ($this->repository->hasPermission($user, $request->shop_id)) {
                            return $this->repository->with('shop')->where('shop_id', '=', $request->shop_id)->where('language', $language);
                        }
                        break;

                    default:
                        return $this->repository->with('shop')->where('language', $language)->where('is_approved', '=', true);
                        break;
                }
            } else {
                if ($request->shop_id) {
                    return $this->repository->with('shop')->where('shop_id', '=', $request->shop_id)->where('is_approved', '=', true)->where('language', $language);
                } else {
                    return $this->repository->with('shop')->where('is_approved', '=', true)->where('language', $language);
                }
            }
        } catch (DurrbarException $e) {
            throw new DurrbarException(SOMETHING_WENT_WRONG, $e->getMessage());
        }
    }

    /**
     * Store a newly created termsAndConditions in storage.
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function store(CreateTermsAndConditionsRequest $request)
    {
        try {
            return $this->repository->storeTermsAndConditions($request);
            // return $this->repository->create($validatedData);
        } catch (DurrbarException $e) {
            throw new DurrbarException(COULD_NOT_CREATE_THE_RESOURCE, $e->getMessage());
        }
    }

    /**
     * Display the specified termsAndConditions.
     *
     * @param  $id
     * @return JsonResponse
     */
    public function show(Request $request, $slug)
    {
        try {
            $language = $request->language ?? DEFAULT_LANGUAGE;
            $termsAndCondition = $this->repository->with('shop')->where('language', $language)->where('slug', '=', $slug)->first();

            return new TermsConditionResource($termsAndCondition);
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND, $e->getMessage());
        }
    }

    /**
     * Update the specified terms and conditions
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(UpdateTermsAndConditionsRequest $request, $id)
    {
        try {
            $request['id'] = $id;

            return $this->updateTermsAndConditions($request);
        } catch (DurrbarException $e) {
            throw new DurrbarException(COULD_NOT_UPDATE_THE_RESOURCE, $e->getMessage());
        }
    }

    /**
     * updateTermsAndConditions
     *
     * @return void
     */
    public function updateTermsAndConditions(UpdateTermsAndConditionsRequest $request)
    {
        $termsAndConditions = $this->repository->findOrFail($request['id']);

        return $this->repository->updateTermsAndConditions($request, $termsAndConditions);
    }

    /**
     * Remove the specified terms and conditions
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id, Request $request)
    {
        $request->merge(['id' => $id]);

        return $this->deleteTermsConditions($request);
    }

    public function deleteTermsConditions(Request $request)
    {
        try {
            $user = $request->user();
            if ($user && ($user->hasPermissionTo(Permission::SUPER_ADMIN) || $user->hasPermissionTo(Permission::STORE_OWNER) || $user->hasPermissionTo(Permission::STAFF))) {
                return $this->repository->findOrFail($request->id)->delete();
            }
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND, $e->getMessage());
        }
    }

    /**
     * approveTerm
     *
     * @return void
     */
    public function approveTerm(Request $request)
    {
        try {
            if (! $request->user()->hasPermissionTo(Permission::SUPER_ADMIN)) {
                throw new DurrbarException(NOT_AUTHORIZED);
            }
            $id = $request->id;
            try {
                $term = $this->repository->findOrFail($id);
            } catch (\Exception $e) {
                throw new ModelNotFoundException(NOT_FOUND);
            }
            $term->is_approved = true;
            $term->save();

            return $term;
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }

    /**
     * disApproveTerm
     *
     * @return void
     */
    public function disApproveTerm(Request $request)
    {
        try {
            if (! $request->user()->hasPermissionTo(Permission::SUPER_ADMIN)) {
                throw new DurrbarException(NOT_AUTHORIZED);
            }
            $id = $request->id;
            try {
                $term = $this->repository->findOrFail($id);
            } catch (\Exception $e) {
                throw new ModelNotFoundException(NOT_FOUND);
            }

            $term->is_approved = false;
            $term->save();

            return $term;
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }
}
