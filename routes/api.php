<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Faxt\Invenbin\Http\Controllers\Api\ProductController;
use Faxt\Invenbin\Http\Controllers\Api\InventoryController;
use Faxt\Invenbin\Http\Controllers\Api\UsageLogController;
use Faxt\Invenbin\Http\Controllers\Api\CategoryController;
use Faxt\Invenbin\Http\Controllers\Api\ProductTypeController;
use Faxt\Invenbin\Http\Controllers\Api\ProductStatusController;
use Faxt\Invenbin\Http\Controllers\Api\BillOfMaterialsController;
use Faxt\Invenbin\Http\Controllers\Api\LookupController;
use Faxt\Invenbin\Http\Controllers\Api\ComponentController;

Route::prefix('api')->group(function () {
    // Products Endpoints
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{guid}', [ProductController::class, 'show']);
    Route::put('/products/{guid}', [ProductController::class, 'update']);
    Route::delete('/products/{guid}', [ProductController::class, 'destroy']);

    // Usage Log Endpoints
    Route::get('/usage-logs', [UsageLogController::class, 'index']);
    Route::post('/usage-logs', [UsageLogController::class, 'store']);
    Route::get('/usage-logs/{id}', [UsageLogController::class, 'show']);
    Route::put('/usage-logs/{id}', [UsageLogController::class, 'update']);
    Route::delete('/usage-logs/{id}', [UsageLogController::class, 'destroy']);

    // Categories Endpoints
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Product Type Endpoints
    Route::get('/product-types', [ProductTypeController::class, 'index']);
    Route::post('/product-types', [ProductTypeController::class, 'store']);
    Route::get('/product-types/{id}', [ProductTypeController::class, 'show']);
    Route::put('/product-types/{id}', [ProductTypeController::class, 'update']);
    Route::delete('/product-types/{id}', [ProductTypeController::class, 'destroy']);

    // Product Status Endpoints
    Route::get('/product-statuses', [ProductStatusController::class, 'index']);
    Route::post('/product-statuses', [ProductStatusController::class, 'store']);
    Route::get('/product-statuses/{id}', [ProductStatusController::class, 'show']);
    Route::put('/product-statuses/{id}', [ProductStatusController::class, 'update']);
    Route::delete('/product-statuses/{id}', [ProductStatusController::class, 'destroy']);

    // User Lists Endpoints
    Route::get('/bom', [BillOfMaterialsController::class, 'index']);
    Route::post('/bom', [BillOfMaterialsController::class, 'store']);
    Route::get('/bom/{guid}', [BillOfMaterialsController::class, 'show']);
    Route::put('/bom/{guid}', [BillOfMaterialsController::class, 'update']);
    Route::delete('/bom/{guid}', [BillOfMaterialsController::class, 'destroy']);

    // User List Items Endpoints
    Route::get('/bom/{list_id}/components', [ComponentController::class, 'index']);
    Route::post('/bom/{list_id}/components', [ComponentController::class, 'store']);
    Route::get('/bom/{list_id}/components/{id}', [ComponentController::class, 'show']);
    Route::put('/bom/{list_id}/components/{id}', [ComponentController::class, 'update']);
    Route::delete('/bom/{list_id}/components/{id}', [ComponentController::class, 'destroy']);

    //Inventory Endpoints
    //Get current inventory levels:
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/{item}', [InventoryController::class, 'show']);
    //Update inventory levels:
    Route::put('/inventory/{item}', [InventoryController::class, 'update']);
    //Set reorder points and trigger notifications:
    Route::put('/inventory/{item}/reorder', [InventoryController::class, 'setReorderPoint']);

    // Lookups
    Route::get('lookups/categories', [LookupController::class, 'categories']);
    Route::get('lookups/units-of-measure', [LookupController::class, 'unitsOfMeasure']);
    Route::get('lookups/dimension-units', [LookupController::class, 'dimensionUnits']);
    Route::get('lookups/statuses', [LookupController::class, 'statuses']);
    Route::get('lookups/types', [LookupController::class, 'types']);

});