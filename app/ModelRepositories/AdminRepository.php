<?php

namespace App\ModelRepositories;

use App\ModelRepositories\Base\DependedRepository;
use App\Models\Admin;
use App\Utils\ConfigHelper;
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
        parent::__construct(ConfigHelper::isSocialLoginEnabled() ? ['user', 'user.socials'] : 'user', $id);
    }

    public function modelClass()
    {
        return Admin::class;
    }

    public function createWithAttributesFromSocial(array $attributes, array $userAttributes, array $userSocialAttributes)
    {
        $userSocialRepository = new UserSocialRepository();
        if (!empty($userAttributes['email']) && ($user = (new UserRepository())->notStrict()->getByEmail($userAttributes['email']))) {
            $userSocialAttributes['user_id'] = $user->id;
            $userSocialRepository->updateOrCreateWithAttributes($userSocialAttributes);
            $this->updateOrCreateWithAttributes(['user_id' => $user->id], $attributes);
        } elseif ($userSocial = $userSocialRepository->notStrict()->getByProvider($userSocialAttributes['provider'], $userSocialAttributes['provider_id'])) {
            $this->updateOrCreateWithAttributes(['user_id' => $userSocial->user->id], $attributes);
        } else {
            $this->createWithAttributes($attributes, $userAttributes, $userSocialAttributes);
        }

        return $this->syncDefaultPermissions();
    }

    public function createWithAttributesFromAdmin(array $attributes, array $userAttributes)
    {
        $this->createWithAttributes($attributes, $userAttributes);
        return $this->syncDefaultPermissions();
    }

    public function createWithAttributes(array $attributes = [], array $userAttributes = [], array $userSocialAttributes = [])
    {
        $attributes['user_id'] = (new UserRepository())->createWithAttributes($userAttributes, $userSocialAttributes)->id;
        return parent::createWithAttributes($attributes);
    }

    public function syncDefaultPermissions()
    {
        // TODO: Sync default permissions
        return $this->model;
    }

    public function updateAvatar($imageFile)
    {
        return $this->updateWithAttributes([
            'avatar_id' => (new HandledFileRepository())->createWithFiler(
                (new ImageFiler())
                    ->fromExisted($imageFile, false, false)
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
            }, ConfigHelper::isSocialLoginEnabled() ? 'user' : null)
                ->queryByIds($ids)
        );
    }
}
