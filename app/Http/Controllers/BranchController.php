<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchStoreRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BranchController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $branchQuery = Branch::query();

        if ($request->filled('filter')) {
            $branchQuery->where('name', 'LIKE', "%{$request->get('filter')}%");
        }

        $branches = $branchQuery->latest()->paginate();

        return Response::view('branch.index', [
            'branches' => $branches,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('branch.create');
    }

    /**
     * @param BranchStoreRequest $branchStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(BranchStoreRequest $branchStoreRequest)
    {
        Branch::create($branchStoreRequest->validated());

        return Response::redirectTo('/branches/create')
            ->with('success', __('crud.created', [
                'resource' => 'branch',
            ]));
    }

    /**
     * @param Branch $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return Response::view('branch.show', [
            'branch' => $branch,
        ]);
    }

    /**
     * @param Branch $branch
     * @param BranchUpdateRequest $branchUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Branch $branch, BranchUpdateRequest $branchUpdateRequest)
    {
        $branch->update(
            $branchUpdateRequest->validated()
        );

        return Response::redirectTo("/branches/{$branch->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'branch',
            ]));
    }

    /**
     * @param Branch $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return Response::redirectTo('/branches')
            ->with('success', __('crud.deleted', [
                'resource' => 'branch',
            ]));
    }
}
