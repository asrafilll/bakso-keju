<?php

namespace App\Http\Controllers;

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
        return Response::view('user.create');
    }
}
