<?php

namespace App\Http\Controllers;

use App\Actions\CreateProductAction;
use App\Actions\UpdateProductAction;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\OrderSource;
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

        /** @var Collection<OrderSource> */
        $orderSources = OrderSource::query()
            ->orderBy('name')
            ->get();

        return Response::view('product.create', [
            'productCategories' => $productCategories,
            'orderSources' => $orderSources,
        ]);
    }

    /**
     * @param ProductStoreRequest $productStoreRequest
     * @param CreateProductAction $createProductAction
     * @return \Illuminate\Http\Response
     */
    public function store(
        ProductStoreRequest $productStoreRequest,
        CreateProductAction $createProductAction
    ) {
        $createProductAction->execute($productStoreRequest->validated());

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
        $product->load(['productInventories.branch', 'productPrices']);

        /** @var Collection<ProductCategory> */
        $productCategories = ProductCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        /** @var Collection<OrderSource> */
        $orderSources = OrderSource::query()
            ->orderBy('name')
            ->get()
            ->map(function (OrderSource $orderSource) use ($product) {
                $orderSource->product_price = $product->productPrices->where('order_source_id', $orderSource->id)->first()->price ?? 0;

                return $orderSource;
            });

        return Response::view('product.show', [
            'product' => $product,
            'productCategories' => $productCategories,
            'orderSources' => $orderSources,
        ]);
    }

    /**
     * @param Product $product
     * @param ProductUpdateRequest $productUpdateRequest
     * @param UpdateProductAction $updateProductAction
     * @return \Illuminate\Http\Response
     */
    public function update(
        Product $product,
        ProductUpdateRequest $productUpdateRequest,
        UpdateProductAction $updateProductAction
    ) {
        $updateProductAction->execute(
            $product,
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

        $product->productPrices()->delete();
        $product->delete();

        return Response::redirectTo('/products')
            ->with('success', __('crud.deleted', [
                'resource' => 'product',
            ]));
    }
}
