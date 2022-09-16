<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategoryStoreRequest;
use App\Http\Requests\ProductCategoryUpdateRequest;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductCategoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $productCategoryQuery = ProductCategory::query();

        if ($request->filled('filter')) {
            $productCategoryQuery->where(
                'name',
                'LIKE',
                "%{$request->get('filter')}%"
            );
        }

        $productCategories = $productCategoryQuery->latest()->paginate();

        return Response::view('product-category.index', [
            'productCategories' => $productCategories,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('product-category.create');
    }

    /**
     * @param ProductCategoryStoreRequest $productCategoryStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ProductCategoryStoreRequest $productCategoryStoreRequest)
    {
        ProductCategory::create($productCategoryStoreRequest->validated());

        return Response::redirectTo('/product-categories/create')
            ->with('success', __('crud.created', [
                'resource' => 'product category',
            ]));
    }

    /**
     * @param ProductCategory $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        return Response::view('product-category.show', [
            'productCategory' => $productCategory,
        ]);
    }

    /**
     * @param ProductCategory $productCategory
     * @param ProductCategoryUpdateRequest $productCategoryUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(ProductCategory $productCategory, ProductCategoryUpdateRequest $productCategoryUpdateRequest)
    {
        $productCategory->update(
            $productCategoryUpdateRequest->validated()
        );

        return Response::redirectTo("/product-categories/{$productCategory->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'product category',
            ]));
    }

    /**
     * @param ProductCategory $productCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return Response::redirectTo('/product-categories')
            ->with('success', __('crud.deleted', [
                'resource' => 'product category',
            ]));
    }
}
