<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderSourceStoreRequest;
use App\Http\Requests\OrderSourceUpdateRequest;
use App\Models\OrderSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OrderSourceController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderSourceQuery = OrderSource::query();

        if ($request->filled('filter')) {
            $orderSourceQuery->where(function ($query) use ($request) {
                $filterables = [
                    'name',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                }
            });
        }

        $orderSources = $orderSourceQuery->latest()->paginate();

        return Response::view('order-source.index', [
            'orderSources' => $orderSources,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('order-source.create');
    }

    /**
     * @param OrderSourceStoreRequest $orderSourceStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(OrderSourceStoreRequest $orderSourceStoreRequest)
    {
        OrderSource::create($orderSourceStoreRequest->validated());

        return Response::redirectTo('/order-sources/create')
            ->with('success', __('crud.created', [
                'resource' => 'order source',
            ]));
    }

    /**
     * @param OrderSource $orderSource
     * @return \Illuminate\Http\Response
     */
    public function show(OrderSource $orderSource)
    {
        return Response::view('order-source.show', [
            'orderSource' => $orderSource,
        ]);
    }

    /**
     * @param OrderSource $orderSource
     * @param OrderSourceUpdateRequest $orderSourceUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(OrderSource $orderSource, OrderSourceUpdateRequest $orderSourceUpdateRequest)
    {
        $orderSource->update(
            $orderSourceUpdateRequest->validated()
        );

        return Response::redirectTo("/order-sources/{$orderSource->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'order source',
            ]));
    }

    /**
     * @param OrderSource $orderSource
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderSource $orderSource)
    {
        $orderSource->delete();

        return Response::redirectTo('/order-sources')
            ->with('success', __('crud.deleted', [
                'resource' => 'order source',
            ]));
    }
}
