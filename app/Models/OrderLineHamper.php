<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLineHamper extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'order_id',
        'product_hamper_id',
        'hamper_name',
        'price',
        'quantity',
        'total',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getIdrPriceAttribute()
    {
        return number_format(
            $this->price,
            0,
            ',',
            '.'
        );
    }

    public function getIdrQuantityAttribute()
    {
        return number_format(
            $this->quantity,
            0,
            ',',
            '.'
        );
    }

    public function getIdrTotalAttribute()
    {
        return number_format(
            $this->total,
            0,
            ',',
            '.'
        );
    }

    public function productHamper()
    {
        return $this->belongsTo(ProductHamper::class, 'product_hamper_id');
    }
}
