<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\Base\IUserRepository;
use App\ModelRepositories\Base\IUserVerifyEmailRepository;
use App\ModelRepositories\UserRepository;

/**
 * Class VerificationController
 * @package App\Http\Controllers\Api\Auth
 * @property IUserRepository $modelRepository
 */
class VerificationController extends ModelApiController
{
    protected function modelRepositoryClass()
    {
        return UserRepository::class;
    }

    public function store(Request $request)
    {
        if ($request->has('_email')) {
            return $this->verifyEmail($request);
        }
        return $this->responseFail();
    }

    protected function verifyEmail(Request $request)
    {
        $this->validated($request, [
            'code' => 'required|string',
        ]);

        if ($this->modelRepository instanceof IUserVerifyEmailRepository) {
            if (is_null(
                $this->modelRepository->skipProtected()
                    ->verifyEmailByCode($request->input('code'))
            )) {
                return $this->responseFail();
            }
        }

        return $this->responseSuccess();
    }
}