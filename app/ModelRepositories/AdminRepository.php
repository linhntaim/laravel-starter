<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ExtendedUserRepository;
use App\ModelRepositories\Base\IUserVerifyEmailRepository;
use App\ModelRepositories\Base\UserVerifyRepositoryTrait;
use App\Models\Admin;
use Illuminate\Http\UploadedFile;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property Admin|null $model
 * @method Admin|null model($id = null)
 * @method Admin|null getById($id, callable $callback = null)
 */
class AdminRepository extends ExtendedUserRepository implements IUserVerifyEmailRepository
{
    use UserVerifyRepositoryTrait;

    public function modelClass()
    {
        return Admin::class;
    }

    public function updateAvatar(UploadedFile $imageFile, $imageName = null)
    {
        return $this->updateWithAttributes([
            'avatar_id' => (new HandledFileRepository())
                ->usePublic()
                ->createWithUploadedImageFile($imageFile, [], null, null, $imageName)
                ->id,
        ]);
    }

    // TODO:

    // TODO
}
