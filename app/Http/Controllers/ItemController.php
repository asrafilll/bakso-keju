<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ItemController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $itemQuery = Item::with(['itemCategory']);

        if ($request->filled('term')) {
            $itemQuery->where('name', 'LIKE', "%{$request->get('term')}%");
        }

        $filterables = [
            'item_category_id',
        ];

        foreach ($filterables as $filterable) {
            if ($request->filled($filterable)) {
                $itemQuery->where($filterable, $request->get($filterable));
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

        $items = $itemQuery->orderBy($sort, $direction)->paginate();

        /** @var Collection<ItemCategory> */
        $itemCategories = ItemCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('item.index', [
            'items' => $items,
            'itemCategories' => $itemCategories,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /** @var Collection<ItemCategory> */
        $itemCategories = ItemCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('item.create', [
            'itemCategories' => $itemCategories,
        ]);
    }

    /**
     * @param ItemStoreRequest $itemStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ItemStoreRequest $itemStoreRequest)
    {
        Item::create($itemStoreRequest->validated());

        return Response::redirectTo('/items/create')
            ->with('success', __('crud.created', [
                'resource' => 'item',
            ]));
    }

    /**
     * @param Item $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        /** @var Collection<ItemCategory> */
        $itemCategories = ItemCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('item.show', [
            'item' => $item,
            'itemCategories' => $itemCategories,
        ]);
    }

    /**
     * @param Item $item
     * @param ItemUpdateRequest $itemUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Item $item, ItemUpdateRequest $itemUpdateRequest)
    {
        $item->update(
            $itemUpdateRequest->validated()
        );

        return Response::redirectTo("/items/{$item->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'item',
            ]));
    }

    /**
     * @param Item $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return Response::redirectTo('/items')
            ->with('success', __('crud.deleted', [
                'resource' => 'item',
            ]));
    }
}
