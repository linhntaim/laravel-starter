<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\DatabaseNotificationRepository;
use App\Models\Admin;

abstract class NotificationController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new DatabaseNotificationRepository();
    }

    protected function getAccountModel(Request $request)
    {
        return $request->user();
    }

    protected function search(Request $request)
    {
        return [
            'notifiable_type' => Admin::class,
            'notifiable_id' => $request->admin()->user_id,
        ];
    }

    public function update(Request $request, $id)
    {
        $this->modelRepository->pinModel()->getByIdBelongedToNotifiable($id, $this->getAccountModel($request));

        if ($request->has('_read')) {
            return $this->responseModel($this->modelRepository->markAsRead());
        }

        return $this->responseFail();
    }
}
