<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RoleController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roleQuery = Role::query();

        if ($request->filled('filter')) {
            $roleQuery->where('name', 'LIKE', "%{$request->get('filter')}%");
        }

        $roles = $roleQuery->latest()->paginate();

        return Response::view('role.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Response::view('role.create');
    }

    /**
     * @param RoleStoreRequest $roleStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $roleStoreRequest)
    {
        Role::create($roleStoreRequest->validated());

        return Response::redirectTo('/roles/create')
            ->with('success', __('crud.created', [
                'resource' => 'role',
            ]));
    }

    /**
     * @param Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return Response::view('role.show', [
            'role' => $role,
        ]);
    }

    /**
     * @param Role $role
     * @param RoleUpdateRequest $roleUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, RoleUpdateRequest $roleUpdateRequest)
    {
        $role->update(
            $roleUpdateRequest->validated()
        );

        return Response::redirectTo("/roles/{$role->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'role',
            ]));
    }

    /**
     * @param Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return Response::redirectTo('/roles')
            ->with('success', __('crud.deleted', [
                'resource' => 'role',
            ]));
    }
}
