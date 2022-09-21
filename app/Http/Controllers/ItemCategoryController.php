<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemCategoryStoreRequest;
use App\Http\Requests\ItemCategoryUpdateRequest;
use App\Models\ItemCategory;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ItemCategoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $itemCategoryQuery = ItemCategory::query()
            ->select([
                'item_categories.*',
                'parent_item_categories.name as parent_name',
            ])
            ->leftJoin('item_categories as parent_item_categories', 'item_categories.parent_id', 'parent_item_categories.id');

        if ($request->filled('filter')) {
            $itemCategoryQuery->where(function ($query) use ($request) {
                $filterables = [
                    'item_categories.name',
                    'parent_item_categories.name',
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

        $itemCategories = $itemCategoryQuery->latest()->paginate();

        return Response::view('item-category.index', [
            'itemCategories' => $itemCategories,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parentItemCategories = ItemCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('item-category.create', [
            'parentItemCategories' => $parentItemCategories,
        ]);
    }

    /**
     * @param ItemCategoryStoreRequest $itemCategoryStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ItemCategoryStoreRequest $itemCategoryStoreRequest)
    {
        ItemCategory::create($itemCategoryStoreRequest->validated());

        return Response::redirectTo('/item-categories/create')
            ->with('success', __('crud.created', [
                'resource' => 'item category',
            ]));
    }

    /**
     * @param ItemCategory $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ItemCategory $itemCategory)
    {
        $parentItemCategories = $itemCategory->subItemCategories()->count() < 1
            ? ItemCategory::query()
            ->where('id', '!=', $itemCategory->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get()
            : new Collection();

        return Response::view('item-category.show', [
            'parentItemCategories' => $parentItemCategories,
            'itemCategory' => $itemCategory,
        ]);
    }

    /**
     * @param ItemCategory $itemCategory
     * @param ItemCategoryUpdateRequest $itemCategoryUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(ItemCategory $itemCategory, ItemCategoryUpdateRequest $itemCategoryUpdateRequest)
    {
        $itemCategory->update(
            $itemCategoryUpdateRequest->validated()
        );

        return Response::redirectTo("/item-categories/{$itemCategory->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'item category',
            ]));
    }

    /**
     * @param ItemCategory $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemCategory $itemCategory)
    {
        try {
            $itemCategory->delete();

            return Response::redirectTo('/item-categories')
                ->with('success', __('crud.deleted', [
                    'resource' => 'item category',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/item-categories')
                ->with('failed', __('Can\'t delete item category which has sub categories'));
        }
    }
}
