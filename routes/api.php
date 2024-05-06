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
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::patch('/products/{id}', [ProductController::class, 'updatePartial']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

// Category routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/tree', [CategoryController::class, 'indexTree']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/tree/{id}', [CategoryController::class, 'showTree']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::patch('/categories/{id}', [CategoryController::class, 'updatePartial']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

// Supplier routes
Route::get('/supplier', [SupplierController::class, 'index']);
Route::get('/supplier/{id}', [SupplierController::class, 'show']);
Route::post('/supplier', [SupplierController::class, 'store']);
Route::put('/supplier/{id}', [SupplierController::class, 'update']);
Route::patch('/supplier/{id}', [CategoryController::class, 'updatePartial']);
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy']);

