<?php

namespace App\Actions;

use App\Models\ProductHamper;
use Illuminate\Support\Facades\DB;

class UpdateProductHamperAction
{
    public function execute(ProductHamper $product, array $data)
    {
        DB::beginTransaction();

        $product->update([
            'name' => data_get($data, 'name'),
            'price' => data_get($data, 'price'),
        ]);

        DB::commit();

        return $product;
    }
}
