<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $productQuery = Product::with(['productCategory']);

        if ($request->filled('term')) {
            $productQuery->where('name', 'LIKE', "%{$request->get('term')}%");
        }

        $filterables = [
            'product_category_id',
        ];

        foreach ($filterables as $filterable) {
            if ($request->filled($filterable)) {
                $productQuery->where($filterable, $request->get($filterable));
            }
        }

        $sortables = [
            'name',
            'price',
            'created_at',
        ];
        $sort = 'created_at';
        $direction = 'desc';

        if ($request->filled('sort') && in_array($request->get('sort'), $sortables)) {
            $sort = $request->get('sort');
        }

        if ($request->filled('direction') && in_array($request->get('direction'), ['asc', 'desc'])) {
            $direction = $request->get('direction');
        }

        $products = $productQuery->orderBy($sort, $direction)->paginate();

        /** @var Collection<ProductCategory> */
        $productCategories = ProductCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('product.index', [
            'products' => $products,
            'productCategories' => $productCategories,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /** @var Collection<ProductCategory> */
        $productCategories = ProductCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('product.create', [
            'productCategories' => $productCategories,
        ]);
    }

    /**
     * @param ProductStoreRequest $productStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $productStoreRequest)
    {
        Product::create($productStoreRequest->validated());

        return Response::redirectTo('/products/create')
            ->with('success', __('crud.created', [
                'resource' => 'product',
            ]));
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->load(['productInventories.branch']);

        /** @var Collection<ProductCategory> */
        $productCategories = ProductCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('product.show', [
            'product' => $product,
            'productCategories' => $productCategories,
        ]);
    }

    /**
     * @param Product $product
     * @param ProductUpdateRequest $productUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Product $product, ProductUpdateRequest $productUpdateRequest)
    {
        $product->update(
            $productUpdateRequest->validated()
        );

        return Response::redirectTo("/products/{$product->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'product',
            ]));
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->productInventories()->count() > 0) {
            return Response::redirectTo('/products')
                ->with('failed', __('Product already have product inventories'));
        }

        $product->delete();

        return Response::redirectTo('/products')
            ->with('success', __('crud.deleted', [
                'resource' => 'product',
            ]));
    }
}
