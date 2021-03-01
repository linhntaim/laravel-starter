<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\WebController;
use App\Http\Requests\Request;
use App\Providers\RouteServiceProvider;
use App\Utils\ClientSettings\Facade;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends WebController
{
    public function __construct()
    {
        parent::__construct();

        $this->setViewBase('auth');
    }

    public function index(Request $request)
    {
        return $this->view('login');
    }

    public function store(Request $request)
    {
        $validator = $this->validated($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator !== true) {
            return redirect()->route('login')
                ->withErrors($validator)
                ->withInput();
        }

        $this->authenticate($request);

        $request->session()->regenerate();

        Facade::fetchFromUser($request->user())->storeCookie();

        return $this->afterLogin();
    }

    protected function afterLogin()
    {
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    protected function authenticate(Request $request)
    {
        $this->ensureIsNotRateLimited($request);

        foreach ($this->authentications($request) as $authentication) {
            if ($this->tryToAuthenticate($request, $authentication)) {
                RateLimiter::clear($this->throttleKey($request));
                return true;
            }
        }

        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    protected function authentications(Request $request)
    {
        return [
            [
                'email' => $request->input('email'), // try to authenticate with email
                'password' => $request->input('password'),
            ],
            [
                'username' => $request->input('email'), // try to authenticate with username
                'password' => $request->input('password'),
            ],
        ];
    }

    protected function tryToAuthenticate(Request $request, $authentication)
    {
        if (is_array($authentication)) {
            return Auth::attempt($authentication, $request->filled('remember'));
        }
        return false;
    }

    protected function ensureIsNotRateLimited(Request $request)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
}
