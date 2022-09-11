<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userQuery = User::query();

        if ($request->filled('filter')) {
            $userQuery->where(function ($query) use ($request) {
                $filterables = [
                    'name',
                    'email',
                ];

                foreach ($filterables as $filterable) {
                    $query->orWhere($filterable, 'LIKE', "%{$request->get('filter')}%");
                }
            });
        }

        $sortables = [
            'name',
            'email',
            'created_at',
        ];
        $sort = 'created_at';
        $direction = 'desc';

        if ($request->filled('sort') && in_array($request->get('sort'), $sortables)) {
            $sort = $request->get('sort');
        }

        if ($request->filled('direction') && in_array($request->get('direction'), ['asc', 'desc'])) {
            $direction = $request->get('direction');
        }

        $userQuery->orderBy($sort, $direction);

        $users = $userQuery->paginate();

        return Response::view('user.index', [
            'users' => $users,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();

        return Response::view('user.create', [
            'roles' => $roles,
        ]);
    }

    /**
     * @param UserStoreRequest $userStoreRequest
     * @return void
     */
    public function store(UserStoreRequest $userStoreRequest)
    {
        /** @var User */
        $user = User::create($userStoreRequest->only([
            'name',
            'email',
            'password',
        ]));

        if ($userStoreRequest->filled('roles')) {
            $user->assignRole(
                Role::query()
                    ->whereIn('id', $userStoreRequest->get('roles'))
                    ->get()
            );
        }

        return Response::redirectTo('/users/create')
            ->with('success', __('crud.created', ['resource' => 'user']));
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $roles = Role::all();
        $user->load(['roles']);

        return Response::view('user.show', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(User $user, UserUpdateRequest $userUpdateRequest)
    {
        $user->email = $userUpdateRequest->get('email');
        $user->name = $userUpdateRequest->get('name');

        if ($userUpdateRequest->filled('password')) {
            $user->password = $userUpdateRequest->get('password');
        }

        $user->save();

        if ($userUpdateRequest->filled('roles')) {
            $user->syncRoles(
                Role::query()
                    ->whereIn('id', $userUpdateRequest->get('roles'))
                    ->get()
            );
        }

        return Response::redirectTo("/users/{$user->id}")
            ->with('success', __('crud.updated', [
                'resource' => 'user',
            ]));
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return Response::redirectTo('/users')
            ->with('success', __('crud.deleted', [
                'resource' => 'user',
            ]));
    }
}
