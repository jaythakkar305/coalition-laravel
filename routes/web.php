<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/product-operations', [ProductController::class, 'create'])->name('products.operations');
Route::post('/product-store', [ProductController::class, 'store'])->name('products.store');
Route::post('/product-update', [ProductController::class, 'update'])->name('products.update');