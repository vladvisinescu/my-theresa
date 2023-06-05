<?php

use App\Models\PriceDiscount;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can reach the products endpoint', function () {
    $response = $this->get('/products');

    $response->assertStatus(200);
});

it('can reach the products endpoint and filter by category', function () {
    // make use of the seeding we created for running the endpoint
    $this->seed();

    $response = $this->get('/products?category=boots');

    expect($response->status())->toBe(200);

    // check that the response contains only products from the boots category
    $categories = collect($response->json()['data'])->pluck('category');

    expect($categories->unique())->toHaveCount(1);
    expect($categories->unique()->first())->toBe('boots');
});

it('can apply the higher discount to a price', function () {
    collect(['sandals', 'boots', 'sneakers'])->each(fn ($category) => ProductCategory::factory()->create(['name' => $category]));

    /* @var Product $product */
    $product = Product::factory()->create([
        'category_id' => ProductCategory::where('name', 'boots')->first()->id,
    ]);

    ProductPrice::factory()->create([
        'product_id' => $product->id,
        'original' => 10000,
    ]);

    PriceDiscount::factory()->create([
        'type' => 'percentage',
        'amount' => 18,
        'target_type' => 'App\Models\Product',
        'target_id' => $product->id,
    ]);

    PriceDiscount::factory()->create([
        'type' => 'percentage',
        'amount' => 35,
        'target_type' => 'App\Models\ProductCategory',
        'target_id' => ProductCategory::where('name', 'boots')->first()->id,
    ]);

    expect($product->price)->toBe([
        "original" => 10000,
        "final" => 6500,
        "discount_percentage" => '35%',
        "currency" => "EUR"
    ]);
});
