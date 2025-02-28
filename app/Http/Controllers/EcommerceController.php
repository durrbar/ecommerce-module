<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Ecommerce\Http\Requests\ProductRequest;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Resources\ProductCollection;
use Modules\Ecommerce\Resources\ProductResource;
use Modules\Ecommerce\Traits\HasVariant;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Searchable\Search;

class EcommerceController extends Controller
{
    use HasVariant;

    private const CACHE_PUBLIC_PRODUCTS = 'public_products_';
    private const CACHE_ADMIN_PRODUCTS = 'admin_products_';
    private const CACHE_FEATURED_PRODUCTS = 'featured_products';
    private const CACHE_LATEST_PRODUCTS = 'latest_products';

    private const ERROR_CREATE = 'Failed to create product';
    private const ERROR_UPDATE = 'Failed to update product';
    private const ERROR_DELETE = 'Failed to delete product';
    private const ERROR_FEATURED = 'Failed to retrieve featured products';
    private const ERROR_LATEST = 'Failed to retrieve latest products';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $isAdmin = $request->is('api/v1/dashboard/products*');
        $cacheKey = ($isAdmin ? self::CACHE_ADMIN_PRODUCTS : self::CACHE_PUBLIC_PRODUCTS) . $request->query('page', 1);
        $cacheDuration = now()->addMinutes(config('cache.duration'));
        $products = Cache::remember($cacheKey, $cacheDuration, function () use ($isAdmin) {
            $query = QueryBuilder::for(Product::class)
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
                )->with('cover')->withCount('reviews')->withAvg('reviews', 'rating');

            if ($isAdmin) {
                $query->allowedFilters([AllowedFilter::exact('publish')])
                    ->allowedSorts('created_at');
            } else {
                $query->where('publish', 'published');
            }

            return $query->paginate(10);
        });

        return response()->json(['products' => 
        new ProductCollection(
            $products
            )
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
            return $this->handleError(self::ERROR_CREATE . ': ' . $e->getMessage(), $request);
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
            return $this->handleError(self::ERROR_UPDATE . ': ' . $e->getMessage(), $request);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            DB::transaction(function () use ($product) {
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
            return $this->handleError(self::ERROR_DELETE . ': ' . $e->getMessage(), null);
        }
    }

    public function featured(): JsonResponse
    {
        try {
            $featureds = Cache::remember(self::CACHE_FEATURED_PRODUCTS, 60 * 60, function () {
                return Product::with(['images'])->limit(6)->get();
            });

            return response()->json(['featureds' => ProductResource::collection($featureds)]);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_FEATURED . ': ' . $e->getMessage(), null);
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
            return $this->handleError(self::ERROR_LATEST . ': ' . $e->getMessage(), null);
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

    private function loadProductRelations(Product $product): Product
    {
        return $product->load(['variants', 'tags', 'images']);
    }

    private function handleProductImages(Product $product, Request $request): void
    {
        $existingImages = $product->images()->get();

        $incomingUrls = $this->filterIncomingImages($request->input('images'), true);
        $incomingFiles = $this->filterIncomingImages($request->file('images'), false);

        $this->deleteOldImages($existingImages, $incomingUrls);
        $this->processNewImageFiles($product, $incomingFiles);
    }

    private function filterIncomingImages($images, bool $isUrl): \Illuminate\Support\Collection
    {
        return collect($images)->filter(function ($image) use ($isUrl) {
            return $isUrl ? is_string($image) : $image instanceof UploadedFile;
        });
    }

    private function deleteOldImages($existingImages, $incomingUrls): void
    {
        foreach ($existingImages as $existingImage) {
            if (!$incomingUrls->contains($existingImage->url)) {
                Storage::delete($existingImage->path);
                $existingImage->delete();
            }
        }
    }

    private function processNewImageFiles(Product $product, $incomingFiles): void
    {
        foreach ($incomingFiles as $file) {
            $fileName = $this->generateUniqueFileName($file);
            $path = "uploads/product/images/$fileName";

            if (extension_loaded('imagick')) {
                $this->storeResizedImage($file, $path);
            } else {
                $file->storeAs('uploads/product/images', $fileName);
            }

            $product->images()->create(['path' => $path]);
        }
    }

    private function storeResizedImage(UploadedFile $image, string $path): void
    {
        $directory = dirname($path);
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $resizedImage = \Intervention\Image\Laravel\Facades\Image::make($image->getPathname())
            ->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($image->getClientOriginalExtension(), 75);

        Storage::put($path, (string) $resizedImage);
    }

    private function clearProductCache(): void
    {
        Cache::forget(self::CACHE_ADMIN_PRODUCTS);
        Cache::forget(self::CACHE_PUBLIC_PRODUCTS);
    }

    /**
     * Handle error responses.
     *
     * @param string $message The error message to be logged and returned in the response.
     * @param Request|null $request The HTTP request that triggered the error, if available.
     * @param int $statusCode The HTTP status code for the response (default is 500).
     * @return JsonResponse A JSON response containing the success status and error message.
     */
    protected function handleError(string $message, ?Request $request = null, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        Log::error($message, [
            'request' => $request ? $request->all() : [],
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Generate a unique filename for an uploaded image.
     *
     * @param UploadedFile $image The uploaded image file.
     * @return string A unique filename based on the original name and current timestamp.
     */
    protected function generateUniqueFileName(UploadedFile $image): string
    {
        $extension = $image->getClientOriginalExtension();
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        return uniqid($originalName . '_', true) . '.' . $extension;
    }
}
