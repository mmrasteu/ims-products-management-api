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
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('product');
Route::get('/products/{id}/category', [ProductController::class, 'showCategory'])->name('product_category');
Route::get('/products/{id}/supplier', [ProductController::class, 'showSupplier'])->name('product_supplier');
Route::post('/products/filter', [ProductController::class, 'filter'])->name('filter_products');
Route::post('/products', [ProductController::class, 'store'])->name('store_products');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('update_product');
Route::patch('/products/{id}', [ProductController::class, 'updatePartial'])->name('partial_update_product');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('delete_products');

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
Route::get('/categories/tree', [CategoryController::class, 'indexTree'])->name('categories_tree');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('category');
Route::get('/categories/{id}/products', [CategoryController::class, 'showProducts'])->name('category_products');
Route::get('/categories/{id}/tree', [CategoryController::class, 'showTree'])->name('category_tree');
Route::post('/categories/filter', [CategoryController::class, 'filter'])->name('categories_filter');
Route::post('/categories', [CategoryController::class, 'store'])->name('store_category');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('update_category');
Route::patch('/categories/{id}', [CategoryController::class, 'updatePartial'])->name('partial_update_category');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('delete_category');

// Supplier routes
Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
Route::get('/suppliers/{id}', [SupplierController::class, 'show'])->name('supplier');
Route::get('/suppliers/{id}/products', [SupplierController::class, 'showProducts'])->name('supplier_products');
Route::post('/suppliers/filter', [SupplierController::class, 'filter'])->name('suppliers_filter');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('store_supplier');
Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('update_supplier');
Route::patch('/suppliers/{id}', [SupplierController::class, 'updatePartial'])->name('partial_update_supplier');
Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('delete_supplier');
