<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManufactureProduct extends Model
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
        'total_line_product_components_quantity',
        'total_line_products_quantity',
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
    public function lineProductComponents()
    {
        return $this
            ->hasMany(ManufactureProductLineProductComponent::class)
            ->orderBy('product_component_name');
    }

    /**
     * @return HasMany
     */
    public function lineProducts()
    {
        return $this
            ->hasMany(ManufactureProductLineProduct::class)
            ->orderBy('product_name');
    }

    /**
     * @return BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return string
     */
    public function getIdrTotalLineProductComponentsQuantityAttribute()
    {
        return number_format(
            $this->total_line_product_components_quantity,
            0,
            ',',
            '.'
        );
    }

    /**
     * @return string
     */
    public function getIdrTotalLineProductsQuantityAttribute()
    {
        return number_format(
            $this->total_line_products_quantity,
            0,
            ',',
            '.'
        );
    }
}
