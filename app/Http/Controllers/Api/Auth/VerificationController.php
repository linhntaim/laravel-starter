<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\Base\IUserRepository;
use App\ModelRepositories\UserRepository;
use App\Models\Base\IHasEmailVerified;

/**
 * Class VerificationController
 * @package App\Http\Controllers\Api\Auth
 * @property IUserRepository $modelRepository
 */
class VerificationController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->enabled()) {
            $this->abort404();
        }
    }

    protected function modelRepositoryClass()
    {
        return UserRepository::class;
    }

    protected function enabled()
    {
        return classImplemented($this->modelRepository->modelClass(), IHasEmailVerified::class);
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

        if (is_null(
            $this->modelRepository->skipProtected()
                ->verifyEmailByCode($request->input('code'))
        )) {
            return $this->responseFail();
        }

        return $this->responseSuccess();
    }
}