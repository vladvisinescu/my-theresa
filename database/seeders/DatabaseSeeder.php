<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\PriceDiscount;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $initialJson = '[
            {"sku": "000001","name": "BV Lean leather ankle boots","category": "boots","price": 89000},
            {"sku": "000002","name": "BV Lean leather ankle boots","category": "boots","price": 99000},
            {"sku": "000003","name": "Ashlington leather ankle boots","category": "boots","price": 71000},
            {"sku": "000004","name": "Naima embellished suede sandals","category": "sandals","price": 79500},
            {"sku": "000005","name": "Nathane leather sneakers","category": "sneakers","price": 59000}
        ]';

        $products = collect(json_decode($initialJson, true));

        // Add more products to DB
        for($x = 6; $x < 100; $x++) {
            $sku = Str::of($x)->padLeft(6, '0');
            $name = fake()->words(mt_rand(3, 5), true);
            $category = ['sandals', 'boots', 'sneakers'][mt_rand(0, 2)];
            $price = mt_rand(10000, 99900);
            $products->push(['sku' => $sku, 'name' => $name, 'category' => $category, 'price' => $price]);
        }

        // Seed initial categories
        collect(['sandals', 'boots', 'sneakers'])->each(fn ($category) => ProductCategory::factory()->create(['name' => $category]));

        foreach ($products as $product) {
            $stored = Product::create([
                'sku' => $product['sku'],
                'name' => $product['name'],
                'category_id' => ProductCategory::where('name', $product['category'])->first()->id,
            ]);

            $productPrice = ProductPrice::create([
                'product_id' => $stored->id,
                'original' => $product['price'],
                'currency' => 'EUR',
            ]);
        }

        // "Products in the boots category have a 30% discount."
        $discount1 = PriceDiscount::create([
            'type' => 'percentage',
            'amount' => 30,
            'target_type' => 'App\Models\ProductCategory',
            'target_id' => ProductCategory::where('name', 'boots')->first()->id
        ]);

        // "The product with sku = 000003 has a 15% discount."
        $discount2 = PriceDiscount::create([
            'type' => 'percentage',
            'amount' => 15,
            'target_type' => 'App\Models\Product',
            'target_id' => Product::where('sku', '000003')->first()->id,
        ]);

    }
}
