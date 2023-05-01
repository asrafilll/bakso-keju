<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLineItem extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'total',
        'discount',
    ];

    /**
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return string
     */
    public function getIdrPriceAttribute()
    {
        return number_format(
            $this->price,
            0,
            ',',
            '.'
        );
    }

    /**
     * @return string
     */
    public function getIdrQuantityAttribute()
    {
        return number_format(
            $this->quantity,
            0,
            ',',
            '.'
        );
    }

    /**
     * @return string
     */
    public function getIdrTotalAttribute()
    {
        return number_format(
            $this->total,
            0,
            ',',
            '.'
        );
    }
}
