<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\WebController;
use App\Http\Requests\Request;
use App\Providers\RouteServiceProvider;
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

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    protected function authenticate(Request $request)
    {
        $this->ensureIsNotRateLimited($request);

        if (!Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ], $request->filled('remember'))) {
            if (!Auth::attempt([
                'username' => $request->input('email'),
                'password' => $request->input('password'),
            ], $request->filled('remember'))) {
                RateLimiter::hit($this->throttleKey($request));

                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey($request));
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
