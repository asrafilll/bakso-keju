<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseLineItem extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_id',
        'item_id',
        'item_name',
        'price',
        'quantity',
        'total',
    ];

    /**
     * @return BelongsTo
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
