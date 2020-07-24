<?php

namespace App\ModelRepositories;

use App\ModelRepositories\Base\DependedRepository;
use App\Models\Admin;
use App\Utils\HandledFiles\Filer\ImageFiler;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property Admin $model
 * @method Admin getById($id, callable $callback = null)
 */
class AdminRepository extends DependedRepository
{
    public function __construct($id = null)
    {
        parent::__construct('user', $id);
    }

    public function modelClass()
    {
        return Admin::class;
    }

    public function updateAvatar($imageFile)
    {
        return $this->updateWithAttributes([
            'avatar_id' => (new HandledFileRepository())->createWithFiler(
                (new ImageFiler())
                    ->fromUploaded($imageFile, null, false)
                    ->imageResize(Admin::MAX_AVATAR_SIZE, Admin::MAX_AVATAR_SIZE)
                    ->imageSave()
                    ->moveToPublic()
            )->id,
        ]);
    }

    /**
     * @param array $ids
     * @return bool
     * @throws
     */
    public function deleteWithIds(array $ids)
    {
        return $this->queryDelete(
            $this->dependedWhere(function ($query) {
                $query->noneProtected();
            })
                ->queryByIds($ids)
        );
    }
}
