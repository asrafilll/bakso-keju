<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImportStoreRequest;
use App\Imports\ProductImport;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductImportController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var Collection<ProductCategory> */
        $productCategories = ProductCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('product.import', [
            'productCategories' => $productCategories,
        ]);
    }

    /**
     * @param ProductImportStoreRequest $productImportStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ProductImportStoreRequest $productImportStoreRequest)
    {
        try {
            (new ProductImport(
                $productImportStoreRequest->get('product_category_id')
            ))->import($productImportStoreRequest->file('file'));

            return Response::redirectTo('/products')
                ->with('success', __('Success importing products'));
        } catch (Exception $e) {
            return Response::redirectTo('/products/import')
                ->with('failed', $e->getMessage());
        }
    }
}
