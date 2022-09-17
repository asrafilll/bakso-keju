<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CustomerController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customerQuery = Customer::query();

        if ($request->filled('filter')) {
            $customerQuery->where(function ($query) use ($request) {
                $filterables = [
                    'name',
                    'percentage_discount',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                }
            });
        }

        $customers = $customerQuery->latest()->paginate();

        return Response::view('customer.index', [
            'customers' => $customers,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('customer.create');
    }

    /**
     * @param CustomerStoreRequest $customerStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $customerStoreRequest)
    {
        Customer::create($customerStoreRequest->validated());

        return Response::redirectTo('/customers/create')
            ->with('success', __('crud.created', [
                'resource' => 'customer',
            ]));
    }

    /**
     * @param Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return Response::view('customer.show', [
            'customer' => $customer,
        ]);
    }

    /**
     * @param Customer $customer
     * @param CustomerUpdateRequest $customerUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Customer $customer, CustomerUpdateRequest $customerUpdateRequest)
    {
        $customer->update(
            $customerUpdateRequest->validated()
        );

        return Response::redirectTo("/customers/{$customer->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'customer',
            ]));
    }

    /**
     * @param Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return Response::redirectTo('/customers')
            ->with('success', __('crud.deleted', [
                'resource' => 'customer',
            ]));
    }
}
