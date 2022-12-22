<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'order_number_prefix',
        'next_order_number',
        'purchase_number_prefix',
        'next_purchase_number',
        'is_main',
    ];

    /**
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(BranchUser::class);
    }
}
