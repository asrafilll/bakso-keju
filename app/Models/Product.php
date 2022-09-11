<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * @return string
     */
    public function getIdrPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }
}
