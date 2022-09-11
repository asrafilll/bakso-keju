<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RoleController extends Controller
{
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
}
