<?php

namespace Modules\Ecommerce\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Repositories\BaseRepository;
use Modules\Ecommerce\Models\Wishlist;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WishlistRepository extends BaseRepository
{
    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
            //
        }
    }

    /**
     * @var array[]
     */
    protected $dataArray = [
        'user_id',
        'product_id',
        'variation_option_id',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Wishlist::class;
    }

    /**
     * @return LengthAwarePaginator|JsonResponse|Collection|mixed
     */
    public function storeWishlist($request)
    {
        try {
            $user_id = $request->user()->id;
            $wishlist = $this->findOneWhere((['user_id' => $user_id, 'product_id' => $request['product_id']]));
            if (empty($wishlist)) {
                $request['user_id'] = $user_id;
                $wishlistInput = $request->only($this->dataArray);

                return $this->create($wishlistInput);
            }
        } catch (\Exception $e) {
            throw new HttpException(400, ALREADY_ADDED_TO_WISHLIST_FOR_THIS_PRODUCT);
        }
    }

    /**
     * @return LengthAwarePaginator|JsonResponse|Collection|mixed
     */
    public function toggleWishlist($request)
    {
        try {
            $user_id = $request->user()->id;
            $wishlist = $this->findOneWhere((['user_id' => $user_id, 'product_id' => $request['product_id']]));
            if (empty($wishlist)) {
                $request['user_id'] = $user_id;
                $wishlistInput = $request->only($this->dataArray);
                $this->create($wishlistInput);

                return true;
            } else {
                $this->delete($wishlist->id);

                return false;
            }
        } catch (\Exception $e) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }
}
