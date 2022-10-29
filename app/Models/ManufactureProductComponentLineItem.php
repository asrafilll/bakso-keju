<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufactureProductComponentLineItem extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'manufacture_product_component_id',
        'product_component_id',
        'product_component_name',
        'price',
        'quantity',
        'total_weight',
        'total_price',
    ];

    /**
     * @return BelongsTo
     */
    public function manufactureProductComponent()
    {
        return $this->belongsTo(ManufactureProductComponent::class);
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
    public function getIdrTotalWeightAttribute()
    {
        return number_format(
            $this->total_weight,
            2,
            ',',
            '.'
        );
    }

    /**
     * @return string
     */
    public function getIdrTotalPriceAttribute()
    {
        return number_format(
            $this->total_price,
            0,
            ',',
            '.'
        );
    }
}
