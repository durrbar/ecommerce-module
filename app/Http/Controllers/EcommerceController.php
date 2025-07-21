<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Ecommerce\Http\Controllers\Traits\HandlesProductOperations;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Resources\ProductCollection;
use Modules\Ecommerce\Resources\ProductResource;
use Modules\Ecommerce\Traits\HasVariant;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Searchable\Search;

class EcommerceController extends Controller
{
    use HandlesProductOperations;
    use HasVariant;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = self::CACHE_PUBLIC_PRODUCTS.$request->query('page', 1);
        $cacheDuration = now()->addMinutes(config('cache.duration'));

        $products = Cache::remember($cacheKey, $cacheDuration, function () {
            return QueryBuilder::for(Product::class)
                ->allowedFields(
                    'id',
                    'slug',
                    'title',
                    'duration',
                    'author_id',
                    'created_at',
                    'total_views',
                    'total_shares',
                    'created_at'
                )->with('cover')->withCount('reviews')->withAvg('reviews', 'rating')->where('publish', 'published')->paginate(10);
        });

        return response()->json(['products' => new ProductCollection(
            $products
        ),
        ]);
    }

    /**
     * Show the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        $cacheKey = "product_{$product->id}";
        $cacheDuration = now()->addMinutes(config('cache.duration')); // Cache duration for 30 minutes

        $product = Cache::remember($cacheKey, $cacheDuration, function () use ($product) {
            // Load relations only if the product is not found in the cache
            return $product->load(['images', 'variants', 'tags', 'reviews'])->loadCount('reviews')->loadAvg('reviews', 'rating');
        });

        return response()->json(['product' => new ProductResource($product)]);
    }

    public function featured(): JsonResponse
    {
        try {
            $featureds = Cache::remember(self::CACHE_FEATURED_PRODUCTS, 60 * 60, function () {
                return Product::with(['images'])->limit(6)->get();
            });

            return response()->json(['featureds' => ProductResource::collection($featureds)]);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_FEATURED.': '.$e->getMessage(), null);
        }
    }

    public function latest(): JsonResponse
    {
        try {
            $latest = Cache::remember(self::CACHE_LATEST_PRODUCTS, 60 * 60, function () {
                return Product::with(['author', 'cover'])
                    ->withCount(['comments as total_comments'])
                    ->limit(5)->get();
            });

            return response()->json(['latest' => $latest]);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_LATEST.': '.$e->getMessage(), null);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query');

        $results = (new Search())
            ->registerModel(Product::class, 'title')
            ->search($query);

        $formattedResults = ProductResource::collection(collect($results)->pluck('searchable'));

        return response()->json(['results' => $formattedResults]);
    }
}
