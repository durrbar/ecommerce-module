<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Ecommerce\Http\Controllers\Traits\HandlesProductOperations;
use Modules\Ecommerce\Http\Requests\ProductRequest;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Resources\ProductCollection;
use Modules\Ecommerce\Resources\ProductResource;
use Modules\Ecommerce\Traits\HasVariant;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EcommerceAdminController extends Controller
{
    use AuthorizesRequests;
    use HandlesProductOperations;
    use HasVariant;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = self::CACHE_ADMIN_PRODUCTS.$request->query('page', 1);
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
                )->with('cover')->withCount('reviews')->withAvg('reviews', 'rating')->allowedFilters([AllowedFilter::exact('publish')])
                ->allowedSorts('created_at')->paginate(10);
        });

        return response()->json(['products' => new ProductCollection(
            $products
        ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = DB::transaction(function () use ($request) {
                // Create the product
                $product = Product::create($request->validated());

                // Handle variants
                $this->syncVariants($product, $request->input('variants', []));

                // Handle images
                $this->handleProductImages($product, $request);

                return $this->loadProductRelations($product);
            });

            $this->clearProductCache();

            return response()->json(['product' => $product, 'message' => 'Product created successfully!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_CREATE.': '.$e->getMessage(), $request);
        }
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

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        try {
            $product = DB::transaction(function () use ($request, $product) {
                // Update the product
                $product->update($request->validated());

                // Handle variants
                $this->syncVariants($product, $request->input('variants', []));

                // Handle images
                $this->handleProductImages($product, $request);

                return $this->loadProductRelations($product);
            });

            Cache::forget("product_{$product->id}");

            return response()->json(['product' => new ProductResource($product)]);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_UPDATE.': '.$e->getMessage(), $request);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            DB::transaction(function () use ($product): void {
                // Delete associated images
                foreach ($product->images as $image) {
                    if (Storage::exists($image->path)) {
                        Storage::delete($image->path);
                    }
                    $image->delete();
                }

                // Delete the product
                $product->delete();
            });

            Cache::forget("product_{$product->id}");

            return response()->json(['message' => 'Product deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_DELETE.': '.$e->getMessage(), null);
        }
    }
}
