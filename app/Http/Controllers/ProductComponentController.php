<?php

namespace App\Http\Controllers;

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
}
