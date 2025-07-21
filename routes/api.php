<?php

use Illuminate\Support\Facades\Route;
use Modules\Ecommerce\Http\Controllers\EcommerceAdminController;
use Modules\Ecommerce\Http\Controllers\EcommerceController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::prefix('v1')->group(function (): void {
    Route::middleware(['auth:sanctum'])->name('dashboard.')->prefix('dashboard')->group(function (): void {
        Route::apiResource('products', EcommerceAdminController::class)->withTrashed()->names('products');
    });

    Route::controller(ECommerceController::class)->name('products.')->prefix('products')->group(function (): void {

        Route::get('featureds', 'featured')->name('featured');

        Route::get('latest', 'latest')->name('latest');

        Route::get('search', 'search')->name('search');
    });

    Route::apiResource('products', ECommerceController::class)->only(['index', 'show']);
});
