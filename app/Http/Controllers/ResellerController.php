<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResellerStoreRequest;
use App\Http\Requests\ResellerUpdateRequest;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ResellerController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $resellerQuery = Reseller::query();

        if ($request->filled('filter')) {
            $resellerQuery->where(function ($query) use ($request) {
                $filterables = [
                    'name',
                    'percentage_discount',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                }
            });
        }

        $resellers = $resellerQuery->latest()->paginate();

        return Response::view('reseller.index', [
            'resellers' => $resellers,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('reseller.create');
    }

    /**
     * @param ResellerStoreRequest $resellerStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ResellerStoreRequest $resellerStoreRequest)
    {
        Reseller::create($resellerStoreRequest->validated());

        return Response::redirectTo('/resellers/create')
            ->with('success', __('crud.created', [
                'resource' => 'reseller',
            ]));
    }

    /**
     * @param Reseller $reseller
     * @return \Illuminate\Http\Response
     */
    public function show(Reseller $reseller)
    {
        return Response::view('reseller.show', [
            'reseller' => $reseller,
        ]);
    }

    /**
     * @param Reseller $reseller
     * @param ResellerUpdateRequest $resellerUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Reseller $reseller, ResellerUpdateRequest $resellerUpdateRequest)
    {
        $reseller->update(
            $resellerUpdateRequest->validated()
        );

        return Response::redirectTo("/resellers/{$reseller->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'reseller',
            ]));
    }

    /**
     * @param Reseller $reseller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reseller $reseller)
    {
        $reseller->delete();

        return Response::redirectTo('/resellers')
            ->with('success', __('crud.deleted', [
                'resource' => 'reseller',
            ]));
    }
}
