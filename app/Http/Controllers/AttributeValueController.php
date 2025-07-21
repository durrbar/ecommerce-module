<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Http\Requests\AttributeValueRequest;
use Modules\Ecommerce\Http\Resources\AttributeValueResource;
use Modules\Ecommerce\Repositories\AttributeValueRepository;
use Prettus\Validator\Exceptions\ValidatorException;

class AttributeValueController extends CoreController
{
    public $repository;

    public function __construct(AttributeValueRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $attributesValue = $this->repository->with('attribute')->all();

        return AttributeValueResource::collection($attributesValue);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function store(AttributeValueRequest $request)
    {
        try {
            if ($this->repository->hasPermission($request->user(), $request->shop_id)) {
                $validatedData = $request->validated();

                return $this->repository->create($validatedData);
            }
            throw new AuthorizationException(NOT_AUTHORIZED);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            return $this->repository->with('attribute')->findOrFail($id);
        } catch (DurrbarException $th) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(AttributeValueRequest $request, $id)
    {
        try {
            $request->id = $id;

            return $this->updateAttributeValues($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_UPDATE_THE_RESOURCE);
        }
    }

    public function updateAttributeValues(AttributeValueRequest $request)
    {
        if ($this->repository->hasPermission($request->user(), $request->shop_id)) {
            try {
                $validatedData = $request->except('id');

                return $this->repository->findOrFail($request->id)->update($validatedData);
            } catch (\Exception $e) {
                throw new ModelNotFoundException(NOT_FOUND);
            }
        }
        throw new AuthorizationException(NOT_AUTHORIZED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id, Request $request)
    {
        try {
            $request->id = $id;

            return $this->destroyAttributeValues($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    /**
     * It deletes an attribute from the database
     *
     * @param Request request The request object.
     * @return JsonResponse
     */
    public function destroyAttributeValues(Request $request)
    {
        $shop_id = $this->repository->findOrFail($request->id)->attribute->shop_id;
        if ($this->repository->hasPermission($request->user(), $shop_id)) {
            $attributesValue = $this->repository->findOrFail($request->id);
            $attributesValue->delete();

            return $attributesValue;
        }
        throw new AuthorizationException(NOT_AUTHORIZED);
    }
}
