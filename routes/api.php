<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', "auth:api"]], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('products', ProductController::class);

    Route::middleware("auth.admin")->prefix("admin")->group(function(){
        Route::get("/users", [UserController::class, "index"]);
        Route::get("/users/{id}/products", [UserController::class, "getUserProducts"]);
        Route::get("/products/{id?}", [ProductController::class, "allProducts"]);
    });
});
Route::get('/products/search/{title}', [ProductController::class, 'search']);