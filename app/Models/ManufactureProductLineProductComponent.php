<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufactureProductLineProductComponent extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'manufacture_product_id',
        'product_component_id',
        'product_component_name',
        'quantity',
    ];

    /**
     * @return BelongsTo
     */
    public function manufactureProduct()
    {
        return $this->belongsTo(ManufactureProduct::class);
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
}
