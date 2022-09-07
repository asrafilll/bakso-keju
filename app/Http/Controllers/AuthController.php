<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class AuthController
{
    /**
     * Undocumented function
     *
     * @param AuthLoginRequest $authLoginRequest
     * @return \Illuminate\Http\Response
     */
    public function login(AuthLoginRequest $authLoginRequest)
    {
        $credential = $authLoginRequest->only(['email', 'password']);

        if (!Auth::attempt($credential, $authLoginRequest->get('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return Response::redirectTo('/');
    }
}
