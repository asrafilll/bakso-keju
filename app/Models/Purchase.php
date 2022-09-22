<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'created_at',
        'purchase_number',
        'branch_id',
        'customer_name',
        'total_line_items_quantity',
        'total_line_items_price',
        'total_price',
        'deleted_at',
    ];

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
    public function purchaseLineItems()
    {
        return $this->hasMany(PurchaseLineItem::class);
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
