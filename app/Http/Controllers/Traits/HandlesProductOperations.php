<?php

namespace Modules\Ecommerce\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Common\Facades\ErrorHelper;
use Modules\Common\Facades\FileHelper;
use Modules\Ecommerce\Models\Product;

trait HandlesProductOperations
{
    private const CACHE_PUBLIC_PRODUCTS = 'api.v1.products.public_';
    private const CACHE_ADMIN_PRODUCTS = 'api.v1.products.admin_';
    private const CACHE_FEATURED_PRODUCTS = 'api.v1.products.featured';
    private const CACHE_LATEST_PRODUCTS = 'api.v1.products.latest';

    // Error messages
    private const ERROR_CREATE = 'Failed to create product';
    private const ERROR_UPDATE = 'Failed to update product';
    private const ERROR_DELETE = 'Failed to delete product';
    private const ERROR_FEATURED = 'Failed to retrieve featured products';
    private const ERROR_LATEST = 'Failed to retrieve latest products';

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
            $path = FileHelper::setFile($file)
                ->setPath('uploads/product/images') // Set the specific path for product images
                ->generateUniqueFileName()
                ->setHeight(1080)
                ->upload()->getPath();

            // Only save the path in DB if upload is successful
            $product->images()->create(['path' => $path]);
        }
    }

    private function clearProductCache(): void
    {
        Cache::forget(self::CACHE_ADMIN_PRODUCTS . '*');
        Cache::forget(self::CACHE_PUBLIC_PRODUCTS . '*');
        Cache::forget(self::CACHE_FEATURED_PRODUCTS);
        Cache::forget(self::CACHE_LATEST_PRODUCTS);
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
        // Use the ErrorHelper facade for error handling
        return ErrorHelper::handleError($message, $request, $statusCode);
    }
}
