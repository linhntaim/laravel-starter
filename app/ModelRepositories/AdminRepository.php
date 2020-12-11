<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\DependedRepository;
use App\ModelRepositories\Base\ExtendedUserRepository;
use App\ModelRepositories\Base\IUserRepository;
use App\Models\Admin;
use App\Utils\SocialLogin;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property Admin $model
 * @method Admin model($id = null)
 * @method Admin getById($id, callable $callback = null)
 */
class AdminRepository extends DependedRepository implements IUserRepository
{
    use ExtendedUserRepository;

    public function __construct($id = null)
    {
        parent::__construct(SocialLogin::getInstance()->enabled() ? ['user', 'user.socials'] : 'user', $id);
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

        // TODO
        return $this->model;
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
            }, SocialLogin::getInstance()->enabled() ? 'user' : null)
                ->queryByIds($ids)
        );
    }

    // TODO: Extra methods

    // TODO
}
