<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\ModelTransformers\AdminAccountTransformer;
use App\Exceptions\AppException;

class AdminAccountController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new AdminRepository();
        $this->modelTransformerClass = AdminAccountTransformer::class;
    }

    public function index(Request $request)
    {
        $model = $this->modelRepository->getById($request->user()->id, false);
        if (empty($model)) {
            throw new AppException(static::__transErrorWithModule('not_found'));
        }
        return $this->responseModel($model);
    }
}
