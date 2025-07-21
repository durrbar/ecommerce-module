<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Http\Requests\AttributeRequest;
use Modules\Ecommerce\Http\Resources\AttributeResource;
use Modules\Ecommerce\Repositories\AttributeRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AttributeController extends CoreController
{
    public $repository;

    public function __construct(AttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|Type[]
     */
    public function index(Request $request)
    {
        $language = $request->language ?? DEFAULT_LANGUAGE;
        $attributes = $this->repository->where('language', $language)->with(['values', 'shop'])->get();

        return AttributeResource::collection($attributes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function store(AttributeRequest $request)
    {
        try {
            if ($this->repository->hasPermission($request->user(), $request->shop_id)) {
                return $this->repository->storeAttribute($request);
            }
            throw new AuthorizationException(NOT_AUTHORIZED);
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return JsonResponse
     */
    public function show(Request $request, $params)
    {

        try {
            $language = $request->language ?? DEFAULT_LANGUAGE;
            if (is_numeric($params)) {
                $params = (int) $params;
                $attribute = $this->repository->with('values')->where('id', $params)->firstOrFail();

                return new AttributeResource($attribute);
            }
            $attribute = $this->repository->with('values')->where('slug', $params)->where('language', $language)->firstOrFail();

            return new AttributeResource($attribute);
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(AttributeRequest $request, $id)
    {
        try {
            $request->id = $id;

            return $this->updateAttribute($request);
        } catch (DurrbarException $e) {
            throw new DurrbarException(COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    public function updateAttribute(AttributeRequest $request)
    {

        if ($this->repository->hasPermission($request->user(), $request->shop_id)) {
            try {
                $attribute = $this->repository->with('values')->findOrFail($request->id);
            } catch (\Exception $e) {
                throw new HttpException(404, NOT_FOUND);
            }

            return $this->repository->updateAttribute($request, $attribute);
        }
        throw new AuthorizationException(NOT_AUTHORIZED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $request->id = $id;

            return $this->deleteAttribute($request);
        } catch (DurrbarException $e) {
            throw new DurrbarException(COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    public function deleteAttribute(Request $request)
    {
        try {
            $attribute = $this->repository->findOrFail($request->id);
        } catch (\Exception $e) {
            throw new HttpException(404, NOT_FOUND);
        }
        if ($this->repository->hasPermission($request->user(), $attribute->shop->id)) {
            $attribute->delete();

            return $attribute;
        }
        throw new AuthorizationException(NOT_AUTHORIZED);
    }

    public function exportAttributes(Request $request, $shop_id)
    {
        $filename = 'attributes-for-shop-id-'.$shop_id.'.csv';
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$filename,
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $list = $this->repository->where('shop_id', $shop_id)->with(['values'])->get()->toArray();

        if (! count($list)) {
            return response()->stream(function (): void {}, 200, $headers);
        }
        // add headers for each column in the CSV download
        array_unshift($list, array_keys($list[0]));

        $callback = function () use ($list): void {
            $FH = fopen('php://output', 'w');
            foreach ($list as $key => $row) {
                if ($key === 0) {
                    $exclude = ['id', 'created_at', 'updated_at', 'slug', 'translated_languages'];
                    $row = array_diff($row, $exclude);
                }
                unset($row['id']);
                unset($row['updated_at']);
                unset($row['slug']);
                unset($row['created_at']);
                unset($row['translated_languages']);
                if (isset($row['values'])) {
                    $row['values'] = implode(',', Arr::pluck($row['values'], 'value'));
                }

                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importAttributes(Request $request)
    {
        $requestFile = $request->file();
        $user = $request->user();
        $shop_id = $request->shop_id;

        if (count($requestFile)) {
            if (isset($requestFile['csv'])) {
                $uploadedCsv = $requestFile['csv'];
            } else {
                $uploadedCsv = current($requestFile);
            }
        }

        if (! $this->repository->hasPermission($user, $shop_id)) {
            throw new DurrbarException(NOT_AUTHORIZED);
        }
        if (isset($shop_id)) {
            $file = $uploadedCsv->storePubliclyAs('csv-files', 'attributes-'.$shop_id.'.'.$uploadedCsv->getClientOriginalExtension(), 'public');

            $attributes = $this->repository->csvToArray(storage_path().'/app/public/'.$file);

            foreach ($attributes as $attribute) {
                if (! isset($attribute['name'])) {
                    throw new DurrbarException('MARVEL_ERROR.WRONG_CSV');
                }
                unset($attribute['id']);
                $attribute['shop_id'] = $shop_id;
                $values = [];
                if (isset($attribute['values'])) {
                    $values = explode(',', $attribute['values']);
                }
                unset($attribute['values']);
                $newAttribute = $this->repository->firstOrCreate($attribute);
                foreach ($values as $value) {
                    $newAttribute->values()->create(['value' => $value]);
                }
            }

            return true;
        }
    }
}
