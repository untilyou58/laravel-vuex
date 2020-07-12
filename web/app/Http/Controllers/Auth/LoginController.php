<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // Add this library

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Authenticated user.
     *
     * @param  Request  \Illuminate\Http\Request
     * @param  $user  $data
     * @return $user  $data
     */
    protected function authenticated(Request $request, $user)
    {
        return $user;
    }

    /**
     * Logged out user.
     *
     * @param  Request  \Illuminate\Http\Request
     * @return response  json
     */
    protected function loggedOut(Request $request)
    {
        // Regenerate session
        $request->session()->regenerate();

        return response()->json();
    }
}
