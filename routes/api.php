<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;

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
Route::post('/login', [AuthController::class, 'login']);

Route::post('/user/create', [AuthController::class, 'create']);

Route::get('/auth-validation', function () {
    $array = [
        'meta' => [],
        'items' => [],
        'status' => false,
        'message' => 'You are not authenticated, Please login',
        'errors' => [],
    ];
    return response($array, 401);
})->name('unauthenticated');

Route::group([
    'middleware' => ['auth:sanctum'],
], function () {
    Route::get('/mytransactions', [TransactionController::class, 'userTransactions']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['admin'])->group(function () {
        Route::post('/category/create', [CategoryController::class, 'create']);
        Route::get('/category/{id}/subcategories', [CategoryController::class, 'getSubcategories']);
        Route::get('/transactions', [TransactionController::class, 'records']);
        Route::post('/transaction/create', [TransactionController::class, 'create']);
        Route::get('/payments', [PaymentController::class, 'records']);
        Route::post('/payment/create', [PaymentController::class, 'create']);
        Route::post('/reports', [TransactionController::class, 'report']);
        Route::post('/monthlyreport', [TransactionController::class, 'monthlyReport']);
    });
});






