<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\PasswordResetRepository;
use App\Utils\StringHelper;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;

abstract class PasswordController extends ModelApiController
{
    use PasswordBrokerTrait;

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

        $user = $this->brokerGetUser([
            'email' => $email,
        ]);
        if (is_null($user)) {
            return $this->responseFail(trans(Password::INVALID_USER));
        }
        if (!$this->brokerTokenExists($user, $request->input('token'))) {
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

        $response = $this->brokerSendResetLink([
            'email' => $request->input('email'),
        ]);

        return $response == Password::RESET_LINK_SENT
            ? $this->responseSuccess()
            : $this->responseFail(trans($response));
    }

    protected function resetValidatedRules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    private function reset(Request $request)
    {
        $this->validated($request, $this->resetValidatedRules());

        $response = $this->brokerReset(
            [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
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
