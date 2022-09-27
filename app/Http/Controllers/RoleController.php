<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $viewPermissions = Permission::query()
            ->where('name', 'LIKE', 'view_%')
            ->orderBy('name')
            ->get();

        $createPermissions = Permission::query()
            ->where('name', 'LIKE', 'create_%')
            ->orderBy('name')
            ->get();

        $updatePermissions =  Permission::query()
            ->where('name', 'LIKE', 'update_%')
            ->orderBy('name')
            ->get();

        $deletePermissions =  Permission::query()
            ->where('name', 'LIKE', 'delete_%')
            ->orderBy('name')
            ->get();

        return Response::view('role.create', [
            'viewPermissions' => $viewPermissions,
            'createPermissions' => $createPermissions,
            'updatePermissions' => $updatePermissions,
            'deletePermissions' => $deletePermissions,
        ]);
    }

    /**
     * @param RoleStoreRequest $roleStoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $roleStoreRequest)
    {
        /** @var Role */
        $role = DB::transaction(function () use ($roleStoreRequest) {
            /** @var Role */
            $role = Role::create($roleStoreRequest->only(['name']));
            $role->permissions()->sync($roleStoreRequest->get('permissions'));

            return $role;
        });

        return Response::redirectTo("/roles/{$role->id}")
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
        $role->load(['permissions']);

        $viewPermissions = Permission::query()
            ->where('name', 'LIKE', 'view_%')
            ->orderBy('name')
            ->get();

        $createPermissions = Permission::query()
            ->where('name', 'LIKE', 'create_%')
            ->orderBy('name')
            ->get();

        $updatePermissions =  Permission::query()
            ->where('name', 'LIKE', 'update_%')
            ->orderBy('name')
            ->get();

        $deletePermissions =  Permission::query()
            ->where('name', 'LIKE', 'delete_%')
            ->orderBy('name')
            ->get();

        return Response::view('role.show', [
            'viewPermissions' => $viewPermissions,
            'createPermissions' => $createPermissions,
            'updatePermissions' => $updatePermissions,
            'deletePermissions' => $deletePermissions,
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
            $roleUpdateRequest->only(['name'])
        );
        $role->permissions()->sync($roleUpdateRequest->get('permissions'));

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
