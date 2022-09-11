<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfilePasswordUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProfilePasswordController extends Controller
{
    /**
     * @param ProfilePasswordUpdateRequest $profilePasswordUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(ProfilePasswordUpdateRequest $profilePasswordUpdateRequest)
    {
        $profilePasswordUpdateRequest->user()->update(
            $profilePasswordUpdateRequest->only(['password'])
        );

        return Response::redirectTo('/profile/password')
            ->with('success', __('crud.updated', [
                'resource' => 'password',
            ]));
    }
}
