<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductAction
{
    /**
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function execute(Product $product, array $data)
    {
        DB::beginTransaction();

        $product->update([
            'name' => data_get($data, 'name'),
            'price' => data_get($data, 'price'),
            'product_category_id' => data_get($data, 'product_category_id'),
        ]);

        $prices = array_map(fn ($price) => [
            'order_source_id' => data_get($price, 'order_source_id'),
            'price' => data_get($price, 'price'),
        ], data_get($data, 'prices', []));

        foreach ($prices as $price) {
            $productPrice = $product
                ->productPrices()
                ->where('order_source_id', $price['order_source_id'])
                ->first();

            if (!$productPrice) {
                $product->productPrices()->create($price);
            } else {
                $productPrice->update(['price' => $price['price']]);
            }
        }

        DB::commit();

        return $product;
    }
}
