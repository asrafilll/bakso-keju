<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemImportStoreRequest;
use App\Imports\ItemImport;
use App\Models\ItemCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ItemImportController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var Collection<ItemCategory> */
        $itemCategories = ItemCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return Response::view('item.import', [
            'itemCategories' => $itemCategories,
        ]);
    }

    /**
     * @param ItemImportStoreRequest $itemImportStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ItemImportStoreRequest $itemImportStoreRequest)
    {
        try {
            (new ItemImport(
                $itemImportStoreRequest->get('item_category_id')
            ))->import($itemImportStoreRequest->file('file'));

            return Response::redirectTo('/items')
                ->with('success', __('Success importing items'));
        } catch (Exception $e) {
            return Response::redirectTo('/items/import')
                ->with('failed', $e->getMessage());
        }
    }
}
