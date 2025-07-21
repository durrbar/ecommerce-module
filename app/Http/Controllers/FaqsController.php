<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Http\Requests\CreateFaqsRequest;
use Modules\Ecommerce\Http\Requests\UpdateFaqsRequest;
use Modules\Ecommerce\Http\Resources\FaqResource;
use Modules\Ecommerce\Models\Faqs;
use Modules\Ecommerce\Repositories\FaqsRepository;
use Modules\Role\Enums\Permission;
use Prettus\Validator\Exceptions\ValidatorException;

class FaqsController extends CoreController
{
    public $repository;

    public function __construct(FaqsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|Faqs[]
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $language = $request->language ?? DEFAULT_LANGUAGE;
        $faqs = $this->fetchFAQs($request)->where('language', $language)->paginate($limit)->withQueryString();
        $data = FaqResource::collection($faqs)->response()->getData(true);

        return formatAPIResourcePaginate($data);
    }

    /**
     * fetchFAQs
     *
     * @return object
     */
    public function fetchFAQs(Request $request)
    {
        $language = $request->language ?? DEFAULT_LANGUAGE;
        try {
            $user = $request->user();

            if ($user) {
                switch ($user) {
                    case $user->hasPermissionTo(Permission::SUPER_ADMIN):
                        return $this->repository
                            ->with('shop')
                            ->whereNotNull('id')
                            ->where('language', $language);
                        break;

                    case $user->hasPermissionTo(Permission::STORE_OWNER):
                        if ($this->repository->hasPermission($user, $request->shop_id)) {
                            return $this->repository
                                ->with('shop')
                                ->where('shop_id', '=', $request->shop_id)
                                ->where('language', $language);
                        } else {
                            return $this->repository
                                ->with('shop')
                                ->where('user_id', '=', $user->id)
                                ->where('language', $language)
                                ->whereIn('shop_id', $user->shops->pluck('id'));
                        }
                        break;

                    case $user->hasPermissionTo(Permission::STAFF):
                        // if ($this->repository->hasPermission($user, $request->shop_id)) {
                        return $this->repository
                            ->with('shop')
                            ->where('shop_id', '=', $request->shop_id)
                            ->where(
                                'language',
                                $language
                            );
                        // }
                        break;

                    default:
                        return $this->repository
                            ->with('shop')
                            ->where('language', $language)
                            ->whereNotNull('id');
                        break;
                }
            } else {
                if ($request->shop_id) {
                    return $this->repository
                        ->with('shop')
                        ->where('shop_id', '=', $request->shop_id)
                        ->where('language', $language)
                        ->whereNotNull('id');
                } else {
                    return $this->repository
                        ->with('shop')
                        ->where('language', $language)
                        ->whereNotNull('id');
                }
            }
        } catch (DurrbarException $e) {
            throw new DurrbarException(SOMETHING_WENT_WRONG, $e->getMessage());
        }
    }

    /**
     * Store a newly created faq in storage.
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function store(CreateFaqsRequest $request)
    {
        try {
            return $this->repository->storeFaqs($request);
            // return $this->repository->create($validatedData);
        } catch (DurrbarException $e) {
            throw new DurrbarException(COULD_NOT_CREATE_THE_RESOURCE, $e->getMessage());
        }
    }

    /**
     * Display the specified faq.
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $faq = $this->repository->with('shop')->findOrFail($id);

            return new FaqResource($faq);
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND, $e->getMessage());
        }
    }

    /**
     * Update the specified faqs
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(UpdateFaqsRequest $request, $id)
    {
        try {
            $request['id'] = $id;

            return $this->updateFaqs($request);
        } catch (DurrbarException $e) {
            throw new DurrbarException(COULD_NOT_UPDATE_THE_RESOURCE, $e->getMessage());
        }
    }

    /**
     * updateFaqs
     *
     * @return void
     */
    public function updateFaqs(UpdateFaqsRequest $request)
    {
        $faqs = $this->repository->findOrFail($request['id']);

        return $this->repository->updateFaqs($request, $faqs);
    }

    /**
     * Remove the specified faqs
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id, Request $request)
    {
        $request->merge(['id' => $id]);

        return $this->deleteFaq($request);
    }

    public function deleteFaq(Request $request)
    {
        try {
            $id = $request->id;
            $user = $request->user();
            if ($user && ($user->hasPermissionTo(Permission::SUPER_ADMIN) || $user->hasPermissionTo(Permission::STORE_OWNER) || $user->hasPermissionTo(Permission::STAFF))) {
                return $this->repository->findOrFail($id)->delete();
            }
            throw new AuthorizationException(NOT_AUTHORIZED);
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND, $e->getMessage());
        }
    }
}
