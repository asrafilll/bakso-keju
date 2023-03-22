<?php

namespace App\Http\Controllers;

use App\Actions\CreateProductHamperAction;
use App\Actions\SearchBranchesAction;
use App\Actions\UpdateProductHamperAction;
use App\Http\Requests\ProductHamperStoreRequest;
use App\Http\Requests\ProductHamperUpdateRequest;
use App\Models\Product;
use App\Models\ProductHamper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductHamperController extends Controller
{
    public function index(Request $request)
    {
        $productQuery = ProductHamper::query();

        if ($request->filled('term')) {
            $productQuery->where('name', 'LIKE', "%{$request->get('term')}%");
        }

        $products = $productQuery->paginate();

        return Response::view('product-hamper.index', [
            'products' => $products,
        ]);
    }

    public function create(
        Request $request,
        SearchBranchesAction $searchBranchesAction
    ) {
        $actions = [
            'fetch-branches' => function () use ($request, $searchBranchesAction) {
                return Response::json(
                    $searchBranchesAction->execute(
                        $request->get('term'),
                        $request->user()
                    )
                );
            },
            'fetch-products' => function () use ($request) {
                $products = Product::query()
                    ->select(['products.*'])
                    ->join('product_inventories', 'products.id', 'product_inventories.product_id')
                    ->join('product_categories as sub_product_categories', 'products.product_category_id', 'sub_product_categories.id')
                    ->join('product_categories', 'sub_product_categories.parent_id', 'product_categories.id')
                    ->where('product_inventories.branch_id', $request->get('branch_id'))
                    ->where('product_inventories.quantity', '>', 0)
                    ->whereRaw("CONCAT_WS(' - ', product_categories.name, sub_product_categories.name, products.name) LIKE ?", [
                        "%{$request->get('term')}%"
                    ])
                    ->orderBy('products.name')
                    ->get();

                return Response::json($products);
            },

            'default' => function () {
                return Response::view('product-hamper.create');
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    public function store(
        ProductHamperStoreRequest $productHamperStoreRequest,
        CreateProductHamperAction $createProductHamperAction
    ) {
        try {
            $createProductHamperAction->execute(
                $productHamperStoreRequest->all()
            );

            return Response::redirectTo('/product-hampers/create')
                ->with('success', __('crud.created', [
                    'resource' => 'product',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/product-hampers/create')
                ->with('failed', $e->getMessage());
        }
    }

    public function show(
        ProductHamper $productHamper,
        Request $request,
    ) {

        $actions = [
            'fetch-products' => function () use ($request) {
                $products = Product::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($products);
            },
            'default' => function () use ($productHamper) {
                return Response::view('product-hamper.show', [
                    'productHamper' => $productHamper,
                ]);
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    public function update(
        ProductHamper $productHamper,
        ProductHamperUpdateRequest $productHamperUpdateRequest,
        UpdateProductHamperAction $updateProductAction
    ) {
        $updateProductAction->execute(
            $productHamper,
            $productHamperUpdateRequest->validated()
        );

        return Response::redirectTo("/product-hampers/{$productHamper->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'product',
            ]));
    }

    public function destroy(ProductHamper $productHamper)
    {
        foreach ($productHamper->productHamperLines as $product) {
            $product->delete();
        }

        $productHamper->delete();

        return Response::redirectTo('/product-hampers')
            ->with('success', __('crud.deleted', [
                'resource' => 'product',
            ]));
    }
}
