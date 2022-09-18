<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderQuery = Order::query()
            ->select([
                'orders.*',
                'order_sources.name as order_source_name',
                'branches.name as branch_name',
            ])
            ->join('order_sources', 'orders.order_source_id', 'order_sources.id')
            ->join('branches', 'orders.branch_id', 'branches.id');

        if ($request->filled('filter')) {
            $orderQuery->where(function ($query) use ($request) {
                $filterables = [
                    'product_name',
                    'branch_name',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                }
            });
        }

        $orders = $orderQuery->latest()->paginate();

        return Response::view('order.index', [
            'orders' => $orders,
        ]);
    }
}
