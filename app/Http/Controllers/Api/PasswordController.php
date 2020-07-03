<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\PasswordResetRepository;
use App\Utils\StringHelper;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;

class PasswordController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new PasswordResetRepository();
    }

    public function index(Request $request)
    {
        if ($request->has('_reset')) {
            return $this->indexReset($request);
        }

        return $this->responseFail();
    }

    private function indexReset(Request $request)
    {
        $this->validated($request, [
            'token' => 'required',
        ]);

        $email = $this->modelRepository->getEmailByToken($request->input('token'));
        if (empty($email)) return $this->abort404();

        $passwordBroker = Password::broker();

        $user = $passwordBroker->getUser([
            'email' => $email,
        ]);
        if (is_null($user)) {
            return $this->responseFail(trans(Password::INVALID_USER));
        }
        if (!$passwordBroker->tokenExists($user, $request->input('token'))) {
            return $this->abort404();
        }

        return $this->responseModel([
            'email' => $email,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('_forgot')) {
            return $this->forgot($request);
        }
        if ($request->has('_reset')) {
            return $this->reset($request);
        }

        return $this->responseFail();
    }

    private function forgot(Request $request)
    {
        $this->validated($request, [
            'email' => 'required|email',
        ]);

        $response = Password::broker()->sendResetLink([
            'email' => $request->input('email'),
        ]);

        return $response == Password::RESET_LINK_SENT
            ? $this->responseSuccess()
            : $this->responseFail(trans($response));
    }

    private function reset(Request $request)
    {
        $this->validated($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $response = Password::broker()->reset(
            [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $request->input('token'),
            ],
            function ($user, $password) {
                $user->password = StringHelper::hash($password);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->responseSuccess()
            : $this->responseFail(trans($response));
    }
}
