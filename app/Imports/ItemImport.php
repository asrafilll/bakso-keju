<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemImport implements ToModel, WithHeadingRow
{
    use Importable;

    /** @var string */
    protected $itemCategoryId;

    public function __construct($itemCategoryId)
    {
        $this->itemCategoryId = $itemCategoryId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Item([
            'name' => $row['name'],
            'price' => round($row['price']),
            'item_category_id' => $this->itemCategoryId,
        ]);
    }
}
