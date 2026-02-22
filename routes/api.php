<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
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
Route::post('login', [ApiController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [ApiController::class, 'logout']);
    Route::get('get-projects', [ApiController::class, 'getProjects']);
    Route::post('add-tracker', [ApiController::class, 'addTracker']);
    Route::post('stop-tracker', [ApiController::class, 'stopTracker']);
    Route::post('upload-photos', [ApiController::class, 'uploadImage']);

    Route::get('customers', [ApiController::class, 'customers']);
    Route::get('customers/{customer}', [ApiController::class, 'customerShow']);

    Route::get('products', [ApiController::class, 'products']);
    Route::get('products/{productService}', [ApiController::class, 'productShow']);

    Route::get('invoices', [ApiController::class, 'invoices']);
    Route::get('invoices/{invoice}', [ApiController::class, 'invoiceShow']);

    Route::get('employees', [ApiController::class, 'employees']);
    Route::get('employees/{employee}', [ApiController::class, 'employeeShow']);
});
