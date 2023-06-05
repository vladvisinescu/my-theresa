<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'original',
        'currency',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function applyDiscounts(array $discounts): Collection
    {
        $discountedPrices = collect();

        foreach ($discounts as $discount) {
            $discountedPrices->push($this->applyDiscount($discount));
        }

        return $discountedPrices;
    }

    public function applyDiscount(PriceDiscount $discount): Collection
    {
        $original = (int) $this->attributes['original'];
        $type = $discount->type;
        $amount = $discount->amount;

        $finalPrice = match ($type) {
            PriceDiscount::TYPE_FIXED => $original - $amount,
            PriceDiscount::TYPE_PERCENTAGE => $original - ($original * ($amount / 100)),
        };

        return collect($discount->toArray())->put('final', (int) $finalPrice);
    }
}
