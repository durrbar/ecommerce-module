<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Http\Requests\AbusiveReportCreateRequest;
use Modules\Ecommerce\Http\Requests\WishlistCreateRequest;
use Modules\Ecommerce\Models\AbusiveReport;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Repositories\WishlistRepository;
use Prettus\Validator\Exceptions\ValidatorException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WishlistController extends CoreController
{
    public $repository;

    public function __construct(WishlistRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|AbusiveReport[]
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 15;
        $wishlist = $this->repository->pluck('product_id');

        return Product::whereIn('id', $wishlist)->paginate($limit);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AbusiveReportCreateRequest  $request
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function store(WishlistCreateRequest $request)
    {
        try {
            return $this->repository->storeWishlist($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_CREATE_THE_RESOURCE);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AbusiveReportCreateRequest  $request
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function toggle(WishlistCreateRequest $request)
    {
        try {
            return $this->repository->toggleWishlist($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
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

            return $this->delete($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    public function delete(Request $request)
    {
        try {
            if (! $request->user()) {
                throw new AuthorizationException(NOT_AUTHORIZED);
            }
            $product = Product::where('id', $request->id)->first();
            $wishlist = $this->repository->where('product_id', $product->id)->where('user_id', auth()->user()->id)->first();
            if (! empty($wishlist)) {
                return $wishlist->delete();
            }
            throw new HttpException(404, NOT_FOUND);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    /**
     * Check in wishlist product for authenticated user
     *
     * @param  int  $product_id
     * @return JsonResponse
     */
    public function in_wishlist(Request $request, $product_id)
    {
        $request->product_id = $product_id;

        return $this->inWishlist($request);
    }

    public function inWishlist(Request $request)
    {
        if (auth()->user() && ! empty($this->repository->where('product_id', $request->product_id)->where('user_id', auth()->user()->id)->first())) {
            return true;
        }

        return false;
    }
}
