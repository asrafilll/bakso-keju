<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'created_at',
        'order_number',
        'order_source_id',
        'branch_id',
        'reseller_order',
        'reseller_id',
        'customer_name',
        'percentage_discount',
        'total_discount',
        'total_line_items_quantity',
        'total_line_items_price',
        'total_price',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function orderSource()
    {
        return $this->belongsTo(OrderSource::class);
    }

    /**
     * @return BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return HasMany
     */
    public function orderLineItems()
    {
        return $this->hasMany(OrderLineItem::class);
    }

    /**
     * @return string
     */
    public function getIdrTotalDiscountAttribute()
    {
        return number_format(
            $this->total_discount,
            0,
            ',',
            '.'
        );
    }

    /**
     * @return string
     */
    public function getIdrTotalLineItemsQuantityAttribute()
    {
        return number_format(
            $this->total_line_items_quantity,
            0,
            ',',
            '.'
        );
    }

    /**
     * @return string
     */
    public function getIdrTotalLineItemsPriceAttribute()
    {
        return number_format(
            $this->total_line_items_price,
            0,
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
