<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Auth;

use App\Events\PasswordResetAutomaticallyEvent;
use App\Events\PasswordResetEvent;
use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\Base\IUserRepository;
use App\ModelRepositories\PasswordResetRepository;
use App\ModelRepositories\UserRepository;
use App\Models\User;
use Closure;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;

/**
 * Class PasswordController
 * @package App\Http\Controllers\Api\Auth
 * @property PasswordResetRepository $modelRepository
 */
abstract class PasswordController extends ModelApiController
{
    /**
     * @var IUserRepository
     */
    protected $userRepository;

    public function __construct()
    {
        parent::__construct();

        if ($userRepositoryClass = $this->getUserRepositoryClass()) {
            $this->userRepository = new $userRepositoryClass;
        }
    }

    protected function modelRepositoryClass()
    {
        return PasswordResetRepository::class;
    }

    /**
     * @return string
     */
    protected abstract function getUserRepositoryClass();

    protected function getPasswordMinLength()
    {
        return $this->userRepository->newModel(false)->getPasswordMinLength();
    }

    /**
     * @return PasswordBroker
     */
    protected function broker()
    {
        return Password::broker();
    }

    protected function brokerGetUser(array $credentials)
    {
        return $this->broker()->getUser($credentials);
    }

    protected function brokerTokenExists(CanResetPassword $user, $token)
    {
        return $this->broker()->tokenExists($user, $token);
    }

    protected function brokerSendResetLink(array $credentials, Closure $callback = null)
    {
        return $this->broker()->sendResetLink($credentials, $callback ? $callback : function ($user, $token) {
            $this->userRepository->model($user)->sendPasswordResetNotification($token);
        });
    }

    protected function brokerReset(array $credentials, Closure $callback)
    {
        return $this->broker()->reset($credentials, $callback);
    }

    public function index(Request $request)
    {
        if ($request->has('_reset')) {
            return $this->indexReset($request);
        }

        return $this->responseFail();
    }

    protected function indexReset(Request $request)
    {
        $this->validated($request, [
            'token' => 'required',
        ]);

        $email = $this->modelRepository->getEmailByToken($request->input('token'));
        if (is_null($email)) {
            return $this->responseFail(trans(Password::INVALID_TOKEN));
        }

        $user = $this->brokerGetUser([
            'email' => $email,
        ]);
        if (is_null($user)) {
            return $this->responseFail(trans(Password::INVALID_USER));
        }
        if (!$this->brokerTokenExists($user, $request->input('token'))) {
            return $this->responseFail(trans(Password::INVALID_TOKEN));
        }

        return $this->responseModel([
            'email' => $email,
        ]);
    }

    protected function isAutomatic()
    {
        return false;
    }

    public function store(Request $request)
    {
        if ($request->has('_forgot')) {
            return $this->isAutomatic() ?
                $this->resetAutomatically($request)
                : $this->forgot($request);
        }
        if ($request->has('_reset')) {
            return $this->reset($request);
        }

        return $this->responseFail();
    }

    protected function forgot(Request $request)
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
            'password' => 'required|string|min:' . $this->getPasswordMinLength() . '|confirmed',
        ];
    }

    protected function reset(Request $request)
    {
        $this->validated($request, $this->resetValidatedRules());

        $response = $this->brokerReset(
            [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'token' => $request->input('token'),
            ],
            function ($user, $password) {
                $this->afterReset($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->responseSuccess()
            : $this->responseFail(trans($response));
    }

    protected function afterReset(User $user, $password)
    {
        (new UserRepository())
            ->withModel($user)
            ->skipProtected()
            ->updatePassword($password);

        event($this->getPasswordResetEvent($user, $password));
    }

    protected function getPasswordResetEvent(User $user, $password)
    {
        return new PasswordResetEvent($user, $password);
    }

    protected function resetAutomatically(Request $request)
    {
        $this->validated($request, [
            'email' => 'required',
        ]);

        ($userRepository = new UserRepository())
            ->notStrict()
            ->pinModel()
            ->getByEmail($request->input('email'));
        if ($userRepository->doesntHaveModel()) {
            return $this->responseFail(trans(Password::INVALID_USER));
        }

        $userRepository
            ->skipProtected()
            ->updatePasswordRandomly($password);
        event($this->getPasswordResetAutomaticallyEvent($userRepository->model(), $password));
        return $this->responseSuccess();
    }

    /**
     * @param User $user
     * @param string $password
     * @return PasswordResetAutomaticallyEvent
     */
    protected function getPasswordResetAutomaticallyEvent(User $user, string $password)
    {
        $eventClass = $this->getPasswordResetAutomaticallyEventClass();
        return new $eventClass($user, $password);
    }

    protected function getPasswordResetAutomaticallyEventClass()
    {
        return PasswordResetAutomaticallyEvent::class;
    }
}
