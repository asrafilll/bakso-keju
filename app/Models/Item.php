<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'item_category_id',
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
    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class)->withDefault([
            'name' => '-'
        ]);
    }

    /**
     * @return HasMany
     */
    public function itemInventories()
    {
        return $this->hasMany(ItemInventory::class);
    }
}
