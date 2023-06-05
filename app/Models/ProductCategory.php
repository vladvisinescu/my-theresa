<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function discounts(): MorphMany
    {
        return $this->morphMany(PriceDiscount::class, 'discountable', 'target_type', 'target_id');
    }
}
