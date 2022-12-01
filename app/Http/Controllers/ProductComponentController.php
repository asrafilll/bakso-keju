<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductComponentStoreRequest;
use App\Http\Requests\ProductComponentUpdateRequest;
use App\Models\ProductComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductComponentController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $productComponentQuery = ProductComponent::query();

        if ($request->filled('filter')) {
            $productComponentQuery->where(function ($query) use ($request) {
                $filterables = [
                    'name',
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

        $productComponents = $productComponentQuery->latest()->paginate();

        return Response::view('product-component.index', [
            'productComponents' => $productComponents
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('product-component.create');
    }

    /**
     * @param ProductComponentStoreRequest $productComponentStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ProductComponentStoreRequest $productComponentStoreRequest)
    {
        ProductComponent::create($productComponentStoreRequest->validated());

        return Response::redirectTo('/product-components/create')
            ->with('success', __('crud.created', [
                'resource' => 'product component',
            ]));
    }

    /**
     * @param ProductComponent $productComponent
     * @return \Illuminate\Http\Response
     */
    public function show(ProductComponent $productComponent)
    {
        return Response::view('product-component.show', [
            'productComponent' => $productComponent,
        ]);
    }

    /**
     * @param ProductComponent $productComponent
     * @param ProductComponentUpdateRequest $productComponentUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(ProductComponent $productComponent, ProductComponentUpdateRequest $productComponentUpdateRequest)
    {
        $productComponent->update(
            $productComponentUpdateRequest->validated()
        );

        return Response::redirectTo("/product-components/{$productComponent->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'product component',
            ]));
    }

    /**
     * @param ProductComponent $productComponent
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductComponent $productComponent)
    {
        $productComponent->delete();

        return Response::redirectTo('/product-components')
            ->with('success', __('crud.deleted', [
                'resource' => 'product component',
            ]));
    }
}
