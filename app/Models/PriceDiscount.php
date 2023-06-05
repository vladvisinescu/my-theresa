<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PriceDiscount extends Model
{
    use HasFactory;

    // Could have used an Enum class if more types existed
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENTAGE = 'percentage';

    protected $fillable = [
        'type',
        'amount',
        'target_type',
        'target_id',
    ];

    public function discountable(): MorphTo
    {
        return $this->morphTo('discountable', 'target_type', 'target_id', 'id');
    }
}
