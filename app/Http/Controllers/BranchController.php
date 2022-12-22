<?php

namespace App\Http\Controllers;

use App\Actions\CreateBranchAction;
use App\Actions\UpdateBranchAction;
use App\Http\Requests\BranchStoreRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
            $branchQuery->where(function ($query) use ($request) {
                $filterables = [
                    'name',
                    'order_number_prefix',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                }
            });
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
        /** @var Collection<User> */
        $users = User::query()
            ->orderBy('name')
            ->get();

        return Response::view('branch.create', [
            'users' => $users,
        ]);
    }

    /**
     * @param BranchStoreRequest $branchStoreRequest
     * @param CreateBranchAction $createBranchAction
     * @return \Illuminate\Http\Response
     */
    public function store(
        BranchStoreRequest $branchStoreRequest,
        CreateBranchAction $createBranchAction
    ) {
        $createBranchAction->execute($branchStoreRequest->validated());

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
        /** @var Collection<User> */
        $users = User::query()
            ->orderBy('name')
            ->get();

        return Response::view('branch.show', [
            'users' => $users,
            'branch' => $branch,
        ]);
    }

    /**
     * @param Branch $branch
     * @param BranchUpdateRequest $branchUpdateRequest
     * @param UpdateBranchAction $updateBranchAction
     * @return \Illuminate\Http\Response
     */
    public function update(
        Branch $branch,
        BranchUpdateRequest $branchUpdateRequest,
        UpdateBranchAction $updateBranchAction
    ) {
        $updateBranchAction->execute(
            $branch,
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
