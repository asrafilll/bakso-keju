<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'product_category_id',
    ];

    /**
     * @return string
     */
    public function getIdrPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    /**
     * @return BelongsTo
     */
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class)->withDefault([
            'name' => '-'
        ]);
    }

    /**
     * @return HasMany
     */
    public function productInventories()
    {
        return $this->hasMany(ProductInventory::class);
    }

    /**
     * @return HasMany
     */
    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }
}
