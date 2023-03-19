<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\CreateItemInventoryAction;
use App\Http\Requests\ItemInventoryStoreRequest;
use App\Models\Item;
use Exception;
use App\Actions\SearchBranchesAction;
use App\Exports\ItemInventoryHistoriesExport;
use App\Models\ItemInventoryHistory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ItemInventoryHistoryController extends Controller
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
                    new ItemInventoryHistoriesExport($request->all() + [
                        'user_id' => $request->user()->id,
                    ]),
                    'item_inventory_histories-' . Carbon::now()->unix() . '.xlsx'
                );
            },
            'default' => function () use ($request) {
                $itemInventoryQuery =   ItemInventoryHistory::query()
                    ->select([
                        'item_inventory_histories.*',
                        'items.name as item_name',
                        'branches.name as branch_name',
                    ])
                    ->join('items', 'item_inventory_histories.item_id', 'items.id')
                    ->join('branches', 'item_inventory_histories.branch_id', 'branches.id')
                    ->join('branch_users', 'item_inventory_histories.branch_id', 'branch_users.branch_id')
                    ->where('branch_users.user_id', $request->user()->id);

                if ($request->filled('term')) {
                    $itemInventoryQuery->where(function ($query) use ($request) {
                        $searchables = [
                            'items.name',
                            'branches.name',
                            'item_inventory_histories.quantity',
                        ];

                        foreach ($searchables as $searchable) {
                            $query->orWhere($searchable, 'LIKE', "%{$request->get('term')}%");
                        }
                    });
                }

                $filterables = [
                    'item_inventory_histories.branch_id' => 'branch_id',
                ];

                foreach ($filterables as $field => $filterable) {
                    if ($request->filled($filterable)) {
                        $itemInventoryQuery->where($field, $request->get($filterable));
                    }
                }

                $itemInventories = $itemInventoryQuery->latest()->paginate();

                return Response::view('item-inventory-history.index', [
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
                return Response::view('item-inventory-history.create');
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

            return Response::redirectTo('/item-inventory-histories/create')
                ->with('success', __('crud.created', [
                    'resource' => 'item_inventories',
                ]));
        } catch (Exception $e) {
            return Response::redirectTo('/item-inventory-histories/create')
                ->with('failed', $e->getMessage());
        }
    }
}
