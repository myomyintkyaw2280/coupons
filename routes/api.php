<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


use App\Http\Controllers\Backend\CouponApiController;
use App\Http\Controllers\Backend\ShopApiController;
use App\Http\Controllers\Backend\AdminApiController;
// use App\Http\Controllers\Backend\AdminAuthApiController;

/*
* Start Api
*/

Route::group(['middleware' => ['api']], function(){
    
    /*Admin User API*/
    // Route::controller(AdminApiController::class)->group(function () {
    //     // Route::post('auth/login', 'login');//email & password
    // });

    /*Coupons*/
    Route::get('{admin_id}/coupons', [CouponApiController::class, 'getCoupons']);
    Route::get('{admin_id}/coupons/{id}', [CouponApiController::class, 'getACoupon']);
    Route::post('{admin_id}/coupons', [CouponApiController::class, 'store']);
    Route::put('{admin_id}/coupons/{id}', [CouponApiController::class, 'update']);
    Route::delete('{admin_id}/coupons/{id}', [CouponApiController::class, 'destroy']);

    /*Shops*/
    Route::get('{admin_id}/shops', [ShopApiController::class, 'getShops']);
    Route::get('{admin_id}/shops/{id}', [ShopApiController::class, 'getAShop']);
    Route::post('{admin_id}/shops', [ShopApiController::class, 'store']);
    Route::put('{admin_id}/shops/{id}', [ShopApiController::class, 'update']);
    Route::delete('{admin_id}/shops/{id}', [ShopApiController::class, 'destroy']);

    /*Coupons Shops*/
    Route::get('{admin_id}/coupons/{coupon_id}/shops', [CouponApiController::class, 'getCouponShops']);
    Route::get('{admin_id}/coupons/{coupon_id}/shops/{shop_id}', [CouponApiController::class, 'getACouponShop']);
    Route::post('{admin_id}/coupons/{coupon_id}', [CouponApiController::class, 'store']);
    Route::delete('{admin_id}/coupons/{coupon_id}/shops/{id}', [CouponApiController::class, 'destroy']);
});