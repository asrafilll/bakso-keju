<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHamper extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = ['name'];

    public function productHamperLines()
    {
        return $this->hasMany(ProductHamperLine::class);
    }
}
