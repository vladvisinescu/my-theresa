<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

    // Eager-load
    protected $with = ['priceData'];

    // Hide from JSON and use computed property instead
    protected $hidden = ['priceData'];

    protected $appends = ['price'];

    protected $fillable = [
        'sku',
        'name',
        'category_id',
    ];

    public function priceData(): HasOne
    {
        return $this->hasOne(ProductPrice::class, 'product_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function discounts(): MorphMany
    {
        return $this->morphMany(PriceDiscount::class, 'discountable', 'target_type', 'target_id');
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getPriceDataArray(),
        );
    }

    protected function getPriceDataArray()
    {
        if (!$this->priceData) { // unlikely to happen, but just in case
            return null;
        }

        $productDiscounts = $this->discounts->first();
        $categoryDiscounts = $this->category->discounts->first();


        if (!$productDiscounts && !$categoryDiscounts) {
            return [
                'original' => $this->priceData->attributes['original'],
                'final' => $this->priceData->attributes['original'],
                'discount_percentage' => null,
                'currency' => 'EUR',
            ];
        }

        $discounts = $this->priceData->applyDiscounts(
            array_filter([$productDiscounts, $categoryDiscounts])
        );

        $highestDiscount = $discounts->sortBy('final')->first();

        return [
            'original' => $this->priceData->attributes['original'],
            'final' => $highestDiscount['final'],
            'discount_percentage' => $highestDiscount['amount'].'%',
            'currency' => 'EUR',
        ];
    }
}
