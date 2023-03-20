<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHamperLine extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = ['product_id', 'product_hamper_id', 'quantity'];

    public function productHamper()
    {
        return $this->belongsTo(ProductHamper::class, 'product_hamper_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
