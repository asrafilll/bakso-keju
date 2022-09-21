<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemInventory extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'item_id',
        'quantity',
    ];

    /**
     * @return BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
