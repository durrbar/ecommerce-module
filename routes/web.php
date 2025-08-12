<?php

use Illuminate\Support\Facades\Route;
use Modules\Ecommerce\Http\Controllers\AbusiveReportController;
use Modules\Ecommerce\Http\Controllers\AnalyticsController;
use Modules\Ecommerce\Http\Controllers\AttributeController;
use Modules\Ecommerce\Http\Controllers\AttributeValueController;
use Modules\Ecommerce\Http\Controllers\AuthorController;
use Modules\Ecommerce\Http\Controllers\CategoryController;
use Modules\Ecommerce\Http\Controllers\FaqsController;
use Modules\Ecommerce\Http\Controllers\FeedbackController;
use Modules\Ecommerce\Http\Controllers\ManufacturerController;
use Modules\Ecommerce\Http\Controllers\NotifyLogsController;
use Modules\Ecommerce\Http\Controllers\ProductController;
use Modules\Ecommerce\Http\Controllers\QuestionController;
use Modules\Ecommerce\Http\Controllers\ResourceController;
use Modules\Ecommerce\Http\Controllers\TermsAndConditionsController;
use Modules\Ecommerce\Http\Controllers\TypeController;
use Modules\Ecommerce\Http\Controllers\WishlistController;
use Modules\Role\Enums\Permission;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group([], function () {
//     Route::resource('ecommerce', EcommerceController::class)->names('ecommerce');
// });

Route::get('top-authors', [AuthorController::class, 'topAuthor']);
Route::get('top-manufacturers', [ManufacturerController::class, 'topManufacturer']);
Route::get('popular-products', [ProductController::class, 'popularProducts']);
Route::get('best-selling-products', [ProductController::class, 'bestSellingProducts']);
Route::get('check-availability', [ProductController::class, 'checkAvailability']);
Route::get('products/calculate-rental-price', [ProductController::class, 'calculateRentalPrice']);
Route::post('import-products', [ProductController::class, 'importProducts']);
Route::post('import-variation-options', [ProductController::class, 'importVariationOptions']);
Route::get('export-products/{shop_id}', [ProductController::class, 'exportProducts']);
Route::get('export-variation-options/{shop_id}', [ProductController::class, 'exportVariableOptions']);
Route::post('generate-description', [ProductController::class, 'generateDescription']);
Route::post('import-attributes', [AttributeController::class, 'importAttributes']);
Route::get('export-attributes/{shop_id}', [AttributeController::class, 'exportAttributes']);

Route::apiResource('products', ProductController::class, [
    'only' => ['index', 'show'],
]);
Route::apiResource('types', TypeController::class, [
    'only' => ['index', 'show'],
]);
Route::apiResource('categories', CategoryController::class, [
    'only' => ['index', 'show'],
]);
Route::apiResource('resources', ResourceController::class, [
    'only' => ['index', 'show'],
]);
Route::apiResource('attributes', AttributeController::class, [
    'only' => ['index', 'show'],
]);
Route::apiResource('questions', QuestionController::class, [
    'only' => ['index', 'show'],
]);
Route::apiResource('feedbacks', FeedbackController::class, [
    'only' => ['index', 'show'],
]);
Route::apiResource('authors', AuthorController::class, [
    'only' => ['index', 'show'],
]);
Route::apiResource('manufacturers', ManufacturerController::class, [
    'only' => ['index', 'show'],
]);

Route::apiResource('faqs', FaqsController::class, [
    'only' => ['index', 'show'],
]);

Route::apiResource('terms-and-conditions', TermsAndConditionsController::class, [
    'only' => ['index', 'show'],
]);

/**
 * ******************************************
 * Authorized Route for Customers only
 * ******************************************
 */
Route::group(['middleware' => ['can:'.Permission::CUSTOMER, 'auth:sanctum', 'email.verified']], function (): void {
    Route::apiResource('questions', QuestionController::class, [
        'only' => ['store'],
    ]);
    Route::apiResource('feedbacks', FeedbackController::class, [
        'only' => ['store'],
    ]);
    Route::apiResource('abusive_reports', AbusiveReportController::class, [
        'only' => ['store'],
    ]);
    Route::get('my-questions', [QuestionController::class, 'myQuestions']);
    Route::get('my-reports', [AbusiveReportController::class, 'myReports']);
    Route::post('wishlists/toggle', [WishlistController::class, 'toggle']);
    Route::apiResource('wishlists', WishlistController::class, [
        'only' => ['index', 'store', 'destroy'],
    ]);
    Route::get('wishlists/in_wishlist/{product_id}', [WishlistController::class, 'in_wishlist']);
    Route::get('my-wishlists', [ProductController::class, 'myWishlists']);
    // Route::apiResource('faqs', FaqsController::class, [
    //     'only' => ['index', 'show'],
    // ]);
    Route::apiResource('notify-logs', NotifyLogsController::class, [
        'only' => ['index', 'show'],
    ]);
    Route::post('notify-log-seen', [NotifyLogsController::class, 'readNotifyLogs']);
    Route::post('notify-log-read-all', [NotifyLogsController::class, 'readAllNotifyLogs']);
});

/**
 * ******************************************
 * Authorized Route for Staff & Store Owner
 * ******************************************
 */
Route::group(
    ['middleware' => ['permission:'.Permission::STAFF.'|'.Permission::STORE_OWNER, 'auth:sanctum', 'email.verified']],
    function (): void {
        Route::apiResource('products', ProductController::class, [
            'only' => ['store', 'update', 'destroy'],
        ]);
        Route::apiResource('resources', ResourceController::class, [
            'only' => ['store'],
        ]);
        Route::apiResource('attributes', AttributeController::class, [
            'only' => ['store', 'update', 'destroy'],
        ]);
        Route::apiResource('attribute-values', AttributeValueController::class, [
            'only' => ['store', 'update', 'destroy'],
        ]);

        Route::apiResource('questions', QuestionController::class, [
            'only' => ['update'],
        ]);
        Route::apiResource('authors', AuthorController::class, [
            'only' => ['store'],
        ]);
        Route::apiResource('manufacturers', ManufacturerController::class, [
            'only' => ['store'],
        ]);
        Route::apiResource('faqs', FaqsController::class, [
            'only' => ['store', 'update', 'destroy'],
        ]);
        Route::get('analytics', [AnalyticsController::class, 'analytics']);
        Route::get('low-stock-products', [AnalyticsController::class, 'lowStockProducts']);
        Route::get('category-wise-product', [AnalyticsController::class, 'categoryWiseProduct']);
        Route::get('category-wise-product-sale', [AnalyticsController::class, 'categoryWiseProductSale']);
        Route::get('draft-products', [ProductController::class, 'draftedProducts']);
        Route::get('products-stock', [ProductController::class, 'productStock']);
        Route::get('top-rate-product', [AnalyticsController::class, 'topRatedProducts']);
    }
);

/**
 * *****************************************
 * Authorized Route for Store owner Only
 * *****************************************
 */
Route::group(
    ['middleware' => ['permission:'.Permission::STORE_OWNER, 'auth:sanctum', 'email.verified']],
    function (): void {
        // Route::get('analytics', [AnalyticsController::class, 'analytics']);
        // Route::apiResource('notify-logs', NotifyLogsController::class, [
        //     'only' => ['index'],
        // ]);

        // Route::post('notify-log-seen', [NotifyLogsController::class, 'readNotifyLogs']);
        // Route::post('notify-log-read-all', [NotifyLogsController::class, 'readAllNotifyLogs']);

        // Route::apiResource('faqs', FaqsController::class, [
        //     'only' => ['store', 'update', 'destroy'],
        // ]);

        Route::apiResource('terms-and-conditions', TermsAndConditionsController::class, [
            'only' => ['store', 'update', 'destroy'],
        ]);

        Route::apiResource('terms-and-conditions', TermsAndConditionsController::class, [
            'only' => ['store', 'update', 'destroy'],
        ]);
    }
);

/**
 * *****************************************
 * Authorized Route for Super Admin only
 * *****************************************
 */
Route::group(['middleware' => ['permission:'.Permission::SUPER_ADMIN, 'auth:sanctum']], function (): void {
    // Route::get('messages/get-conversations/{shop_id}', [ConversationController::class, 'getConversationByShopId']);
    // Route::get('analytics', [AnalyticsController::class, 'analytics']);
    Route::apiResource('types', TypeController::class, [
        'only' => ['store', 'update', 'destroy'],
    ]);
    Route::apiResource('categories', CategoryController::class, [
        'only' => ['store', 'update', 'destroy'],
    ]);
    Route::apiResource('resources', ResourceController::class, [
        'only' => ['update', 'destroy'],
    ]);
    Route::apiResource('questions', QuestionController::class, [
        'only' => ['destroy'],
    ]);
    Route::apiResource('feedbacks', FeedbackController::class, [
        'only' => ['update', 'destroy'],
    ]);
    Route::apiResource('abusive_reports', AbusiveReportController::class, [
        'only' => ['index', 'show', 'update', 'destroy'],
    ]);
    Route::post('abusive_reports/accept', [AbusiveReportController::class, 'accept']);
    Route::post('abusive_reports/reject', [AbusiveReportController::class, 'reject']);
    Route::apiResource('authors', AuthorController::class, [
        'only' => ['update', 'destroy'],
    ]);
    Route::apiResource('manufacturers', ManufacturerController::class, [
        'only' => ['update', 'destroy'],
    ]);
    Route::apiResource('notify-logs', NotifyLogsController::class, [
        'only' => ['destroy'],
    ]);
    // Route::apiResource('faqs', FaqsController::class, [
    //     'only' => ['store', 'update', 'destroy'],
    // ]);
    Route::post('approve-terms-and-conditions', [TermsAndConditionsController::class, 'approveTerm']);
    Route::post('disapprove-terms-and-conditions', [TermsAndConditionsController::class, 'disApproveTerm']);
});
