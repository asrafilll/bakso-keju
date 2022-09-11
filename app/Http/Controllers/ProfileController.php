<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProfileController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Response::view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * @param ProfileUpdateRequest $profileUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileUpdateRequest $profileUpdateRequest)
    {
        $profileUpdateRequest->user()->update(
            $profileUpdateRequest->validated()
        );

        return Response::redirectTo('/profile')
            ->with('success', __('crud.updated', [
                'resource' => 'profile',
            ]));
    }
}
