<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHamper extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'branch_id',
        'name',
        'charge',
        'product_hamper_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function productHamperLines()
    {
        return $this->hasMany(ProductHamperLine::class);
    }

    /**
     * @return string
     */
    public function getIdrPriceAttribute()
    {
        return number_format($this->charge, 0, ',', '.');
    }
}
