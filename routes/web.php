<?php

use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'index']);
});
