<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManufactureProductComponent extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'created_at',
        'branch_id',
        'created_by',
        'order_number',
        'total_line_items_quantity',
        'total_line_items_weight',
        'total_line_items_price',
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
    public function manufactureProductComponentLineItems()
    {
        return $this->hasMany(ManufactureProductComponentLineItem::class);
    }

    /**
     * @return BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
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
    public function getIdrTotalLineItemsWeightAttribute()
    {
        return number_format(
            $this->total_line_items_weight,
            2,
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
}
