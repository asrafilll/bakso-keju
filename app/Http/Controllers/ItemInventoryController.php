<?php

namespace App\Http\Controllers;

use App\Actions\SearchBranchesAction;
use App\Models\ItemInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ItemInventoryController extends Controller
{
    /**
     * @param Request $request
     * @param SearchBranchesAction $searchBranchesAction
     * @return \Illuminate\Http\Response
     */
    public function index(
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
            'default' => function () use ($request) {
                $itemInventoryQuery = ItemInventory::query()
                    ->select([
                        'item_inventories.*',
                        'items.name as item_name',
                        'branches.name as branch_name',
                    ])
                    ->join('items', 'item_inventories.item_id', 'items.id')
                    ->join('branches', 'item_inventories.branch_id', 'branches.id')
                    ->join('branch_users', 'item_inventories.branch_id', 'branch_users.branch_id')
                    ->where('branch_users.user_id', $request->user()->id);

                if ($request->filled('term')) {
                    $itemInventoryQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'items.name',
                            'branches.name',
                            'item_inventories.quantity',
                        ];

                        foreach ($searchables as $searchable) {
                            $query->orWhere($searchable, 'LIKE', "%{$request->get('term')}%");
                        }
                    });
                }

                $filterables = [
                    'item_inventories.branch_id' => 'branch_id',
                ];

                foreach ($filterables as $field => $filterable) {
                    if ($request->filled($filterable)) {
                        $itemInventoryQuery->where($field, $request->get($filterable));
                    }
                }

                $itemInventories = $itemInventoryQuery->latest()->paginate();

                return Response::view('item-inventory.index', [
                    'itemInventories' => $itemInventories,
                ]);
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }
}
