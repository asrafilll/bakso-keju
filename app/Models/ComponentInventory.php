<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentInventory extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'branch_id',
        'product_component_id',
        'quantity',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function productComponent()
    {
        return $this->belongsTo(ProductComponent::class);
    }
}
