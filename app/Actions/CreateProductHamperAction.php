<?php

namespace App\Actions;

use App\Models\Branch;
use App\Models\ManufactureProduct;
use App\Models\ManufactureProductLineProduct;
use App\Models\ManufactureProductLineProductComponent;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductComponentInventory;
use App\Models\ProductHamper;
use App\Models\ProductHamperLine;
use App\Models\ProductInventory;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateProductHamperAction
{
    /**
     * @param array $data
     * @param User $authenticatedUser
     * @return ProductHamper
     */
    public function execute(array $data)
    {
        DB::beginTransaction();

        $productHampers = ProductHamper::Create([
            'name' => $data['name'],
            'price' => $data['price'],
        ]);

        /** @var Collection */
        $lineProducts = new Collection($data['products']);

        foreach ($lineProducts as $lineProduct) {
            ProductHamperLine::create([
                'product_id' => $lineProduct['product_id'],
                'product_hamper_id' => $productHampers->id,
                'quantity' => intval($lineProduct['quantity']),
            ]);
        }

        DB::commit();

        return $productHampers;
    }
}
