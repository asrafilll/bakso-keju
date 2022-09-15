<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
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
        $productQuery = Product::query();

        if ($request->filled('filter')) {
            $productQuery->where('name', 'LIKE', "%{$request->get('filter')}%");
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


        return Response::view('product.index', [
            'products' => $products,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('product.create');
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
        return Response::view('product.show', [
            'product' => $product,
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
}
