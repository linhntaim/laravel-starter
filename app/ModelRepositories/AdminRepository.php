<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ExtendedUserRepository;
use App\Models\Admin;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property Admin $model
 * @method Admin model($id = null)
 * @method Admin getById($id, callable $callback = null)
 */
class AdminRepository extends ExtendedUserRepository
{
    public function modelClass()
    {
        return Admin::class;
    }

    public function updateAvatar($imageFile)
    {
        return $this->updateWithAttributes([
            'avatar_id' => (new HandledFileRepository())
                ->usePublic()
                ->createWithUploadedImageFile($imageFile)
                ->id,
        ]);
    }

    // TODO:

    // TODO
}
