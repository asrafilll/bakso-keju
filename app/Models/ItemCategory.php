<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_id',
    ];

    /**
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * @return HasMany
     */
    public function subItemCategories()
    {
        return $this->hasMany(ItemCategory::class, 'parent_id');
    }

    /**
     * @return BelongsTo
     */
    public function parentItemCategory()
    {
        return $this->belongsTo(ItemCategory::class, 'parent_id');
    }
}
