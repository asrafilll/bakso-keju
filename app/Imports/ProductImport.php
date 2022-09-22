<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    use Importable;

    /** @var string */
    protected $productCategoryId;

    public function __construct($productCategoryId)
    {
        $this->productCategoryId = $productCategoryId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Product([
            'name' => $row['name'],
            'price' => round($row['price']),
            'product_category_id' => $this->productCategoryId,
        ]);
    }
}
