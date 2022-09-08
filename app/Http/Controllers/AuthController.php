<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthSigninRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class AuthController
{
    /**
     * Undocumented function
     *
     * @param AuthSigninRequest $authSigninRequest
     * @return \Illuminate\Http\Response
     */
    public function signin(AuthSigninRequest $authSigninRequest)
    {
        $credential = $authSigninRequest->only(['email', 'password']);

        if (!Auth::attempt($credential, $authSigninRequest->get('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return Response::redirectTo('/');
    }

    /**
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function signout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Response::redirectTo('/');
    }
}
