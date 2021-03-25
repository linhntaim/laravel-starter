<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin\Account;

use App\Http\Controllers\Api\Account\AccountController as BaseAccountController;
use App\Http\Controllers\Api\Admin\AdminAccountTrait;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\ModelRepositories\Base\IUserRepository;
use App\ModelResources\AdminAccountResource;
use App\Models\Admin;

/**
 * Class AccountController
 * @package App\Http\Controllers\Api\Admin\Account
 * @property AdminRepository|IUserRepository $modelRepository
 */
class AccountController extends BaseAccountController
{
    use AdminAccountTrait;

    protected function getAccountRepositoryClass()
    {
        return AdminRepository::class;
    }

    protected function getAccountResourceClass()
    {
        return AdminAccountResource::class;
    }

    public function store(Request $request)
    {
        $this->modelRepository->model($this->getAccountModel($request));

        if ($request->has('_avatar')) {
            return $this->updateAvatar($request);
        }
        if ($request->has('_avatar_by_handled_file')) {
            return $this->updateAvatarByHandledFile($request);
        }
        if ($request->has('_information')) {
            return $this->updateInformation($request);
        }

        return parent::store($request);
    }

    private function updateAvatar(Request $request)
    {
        $this->validated($request, [
            'image' => [
                'required',
                'image',
                sprintf('dimensions:min_width=%d,min_height=%d', Admin::MAX_AVATAR_SIZE, Admin::MAX_AVATAR_SIZE),
            ],
        ]);

        return $this->responseModel(
            $this->modelRepository->updateAvatar($request->file('image'), $request->input('name'))
        );
    }

    private function updateAvatarByHandledFile(Request $request)
    {
        $this->validated($request, [
            'file_id' => [
                'required',
                'exists:handled_files,id',
            ],
        ]);

        return $this->responseModel(
            $this->modelRepository->updateWithAttributes([
                'avatar_id' => $request->input('file_id'),
            ])
        );
    }

    private function updateInformation(Request $request)
    {
        $this->validated($request, [
            'display_name' => 'required|max:255',
        ]);

        return $this->responseModel(
            $this->modelRepository->updateWithAttributes([
                'display_name' => $request->input('display_name'),
            ])
        );
    }

    // TODO:　Extra handles

    // TODO
}
