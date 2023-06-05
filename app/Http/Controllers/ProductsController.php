<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductsController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query();

        if ($request->input('category')) {
            $products->whereHas('category', function ($query) use ($request) {
                $query->where('name', $request->input('category'));
            });
        }

        if ($request->input('priceLessThan')) {
            $products->whereHas('priceData', function ($query) use ($request) {
                $query->where('original', '<=', $request->input('priceLessThan'));
            });
        }

        $products = $products->limit(5)->get();

        return ProductResource::collection($products);
    }
}
