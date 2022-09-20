<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategoryStoreRequest;
use App\Http\Requests\ProductCategoryUpdateRequest;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Database\Eloquent\Collection;
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
        $productCategoryQuery = ProductCategory::query()
            ->select([
                'product_categories.*',
                'parent_product_categories.name as parent_name',
            ])
            ->leftJoin('product_categories as parent_product_categories', 'product_categories.parent_id', 'parent_product_categories.id');

        if ($request->filled('filter')) {
            $productCategoryQuery->where(function ($query) use ($request) {
                $filterables = [
                    'product_categories.name',
                    'parent_product_categories.name',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere(
                        $filterable,
                        'LIKE',
                        "%{$request->get('filter')}%"
                    );
                }
            });
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
        $parentProductCategories = ProductCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('product-category.create', [
            'parentProductCategories' => $parentProductCategories,
        ]);
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
        $parentProductCategories = $productCategory->subProductCategories()->count() < 1
            ? ProductCategory::query()
            ->where('id', '!=', $productCategory->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get()
            : new Collection();

        return Response::view('product-category.show', [
            'parentProductCategories' => $parentProductCategories,
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
        try {
            $productCategory->delete();

            return Response::redirectTo('/product-categories')
                ->with('success', __('crud.deleted', [
                    'resource' => 'product category',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/product-categories')
                ->with('failed', __('Can\'t delete product category which has sub categories'));
        }
    }
}
