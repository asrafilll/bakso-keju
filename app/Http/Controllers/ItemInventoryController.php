<?php

namespace App\Http\Controllers;

use App\Actions\CreateItemInventoryAction;
use App\Actions\SearchBranchesAction;
use App\Exports\ItemInventoriesExport;
use App\Http\Requests\ItemInventoryStoreRequest;
use App\Models\Item;
use App\Models\ItemInventory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

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
            'export' => function () use ($request) {
                return Excel::download(
                    new ItemInventoriesExport($request->all() + [
                        'user_id' => $request->user()->id,
                    ]),
                    'item_inventories-' . Carbon::now()->unix() . '.xlsx'
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

    /**
     * @param Request
     * @param SearchBranchesAction $searchBranchesAction
     * @return \Illuminate\Http\Response
     */
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
            'fetch-items' => function () use ($request) {
                $items = Item::query()
                    ->where('name', 'LIKE', "%{$request->get('term')}%")
                    ->orderBy('name')
                    ->get();

                return Response::json($items);
            },
            'default' => function () {
                return Response::view('item-inventory.create');
            },
        ];

        return $actions[$request->get('action', 'default')]();
    }

    /**
     * @param ItemInventoryStoreRequest $inventoryStoreRequest
     * @param CreateItemInventoryAction $createInventoryAction
     * @return \Illuminate\Http\Response
     */
    public function store(
        ItemInventoryStoreRequest $inventoryStoreRequest,
        CreateItemInventoryAction $createInventoryAction
    ) {
        try {
            $createInventoryAction->execute(
                $inventoryStoreRequest->all(),
                $inventoryStoreRequest->user()
            );

            return Response::redirectTo('/item-inventories/create')
                ->with('success', __('crud.created', [
                    'resource' => 'item_inventories',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/item-inventories/create')
                ->with('failed', $e->getMessage());
        }
    }
}
