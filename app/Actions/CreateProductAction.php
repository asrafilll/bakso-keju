<?php

namespace App\Actions;

use App\Models\Product;

class CreateProductAction
{
    /**
     * @param array $data
     * @return Product
     */
    public function execute(array $data)
    {
        /** @var Product */
        $product = Product::create([
            'name' => data_get($data, 'name'),
            'price' => data_get($data, 'price'),
            'product_category_id' => data_get($data, 'product_category_id'),
        ]);

        $product->productPrices()->createMany(
            array_map(fn ($price) => [
                'order_source_id' => data_get($price, 'order_source_id'),
                'price' => data_get($price, 'price'),
            ], data_get($data, 'prices'))
        );

        return $product;
    }
}
