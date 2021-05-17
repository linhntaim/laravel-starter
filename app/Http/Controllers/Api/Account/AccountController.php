<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Account;

use App\Exceptions\AppException;
use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\Base\IUserRepository;
use App\ModelRepositories\UserRepository;
use App\Models\ActivityLog;
use App\Models\Base\IUser;
use App\Rules\CurrentPasswordRule;
use App\Utils\SocialLogin;
use Illuminate\Validation\Rule;

/**
 * Class AccountController
 * @package App\Http\Controllers\Api\Account
 * @property IUserRepository $modelRepository
 */
abstract class AccountController extends ModelApiController
{
    protected function modelRepositoryClass()
    {
        return $this->getAccountRepositoryClass();
    }

    protected function modelResourceClass()
    {
        return $this->getAccountResourceClass();
    }

    protected abstract function getAccountRepositoryClass();

    protected abstract function getAccountResourceClass();

    /**
     * @param Request $request
     * @return IUser
     */
    protected function getAccountModel(Request $request)
    {
        return $request->user();
    }

    public function index(Request $request)
    {
        $model = $this->modelRepository->model($this->getAccountModel($request));
        if (empty($model)) {
            throw new AppException(static::__transErrorWithModule('not_found'));
        }
        if ($request->has('_login') && !$request->hasImpersonator()) {
            $this->logAction(ActivityLog::ACTION_LOGIN);
        }
        return $this->responseModel(
            $model,
            $request->hasImpersonator() ? [
                'impersonator' => $this->modelTransform($request->impersonator()),
            ] : []
        );
    }

    public function store(Request $request)
    {
        $this->modelRepository->model($this->getAccountModel($request));

        if ($request->has('_last_access')) {
            return $this->updateLastAccess($request);
        }
        if ($request->has('_email')) {
            return $this->updateEmail($request);
        }
        if ($request->has('_password')) {
            return $this->updatePassword($request);
        }

        return $this->responseFail();
    }

    private function updateLastAccess(Request $request)
    {
        if ($this->modelRepository instanceof IUserRepository) {
            return $this->responseModel(
                $this->modelRepository->skipProtected()->updateLastAccessedAt()
            );
        }
        return $this->responseFail();
    }

    private function updateEmail(Request $request)
    {
        $currentUser = $request->user();

        $rules = [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($currentUser->id)->whereNull('deleted_at'),
            ],
        ];
        if (!SocialLogin::getInstance()->enabled() || $currentUser->hasPassword) {
            $rules['current_password'] = [
                'required',
                (new CurrentPasswordRule())
                    ->overrideMessage($this->__transErrorWithModule('current_password.current_password_as_password')),
            ];
        }

        $this->validated($request, $rules);

        return $this->responseModel(
            (new UserRepository($currentUser))->skipProtected()->updateWithAttributes([
                'email' => $request->input('email'),
            ])
        );
    }

    private function updatePassword(Request $request)
    {
        $currentUser = $request->user();

        $rules = [
            'password' => ['required', 'string', sprintf('min:%d', $this->getAccountModel($request)->getPasswordMinLength()), 'confirmed'],
        ];
        if (!SocialLogin::getInstance()->enabled() || $currentUser->hasPassword) {
            $rules['current_password'] = ['required', new CurrentPasswordRule()];
        }
        $this->validated($request, $rules, [
            'password.confirmed' => $this->__transErrorWithModule('password.confirmed_new'),
        ]);

        return $this->responseModel(
            (new UserRepository($currentUser))->skipProtected()->updateWithAttributes([
                'password' => $request->input('password'),
            ])
        );
    }
}
