<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Account;

use App\Exceptions\AppException;
use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\Base\IUserRepository;
use App\Models\ActivityLog;

abstract class BaseAccountController extends ModelApiController
{
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
        if ($request->has('_login')) {
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
        if ($request->has('_last_access')) {
            return $this->updateLastAccess($request);
        }
        return $this->responseFail();
    }

    private function updateLastAccess(Request $request)
    {
        if ($this->modelRepository instanceof IUserRepository) {
            return $this->responseModel(
                $this->modelRepository->updateLastAccessedAt()
            );
        }
        return $this->responseFail();
    }
}
