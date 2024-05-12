<?php

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/status', function () {
    return ['message' => 'API IMS Products Management', 'status' => 200];
});

// Product routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{id}/category', [ProductController::class, 'showCategory']);
Route::get('/products/{id}/supplier', [ProductController::class, 'showSupplier']);
Route::post('/products/filter', [ProductController::class, 'filter']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::patch('/products/{id}', [ProductController::class, 'updatePartial']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

// Category routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/tree', [CategoryController::class, 'indexTree']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/{id}/products', [CategoryController::class, 'showProducts']);
Route::get('/categories/{id}/tree', [CategoryController::class, 'showTree']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::patch('/categories/{id}', [CategoryController::class, 'updatePartial']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

// Supplier routes
Route::get('/suppliers', [SupplierController::class, 'index']);
Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
Route::get('/suppliers/{id}/products', [SupplierController::class, 'showProducts']);
Route::post('/suppliers', [SupplierController::class, 'store']);
Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
Route::patch('/suppliers/{id}', [CategoryController::class, 'updatePartial']);
Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
