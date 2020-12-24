<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\ModelRepositories\UserRepository;
use App\ModelRepositories\UserSocialRepository;
use App\Models\Base\ExtendedUserModel;
use App\Models\Base\IUser;
use App\Models\User;
use App\Utils\SocialLogin;

/**
 * Class ExtendedUserRepository
 * @package App\ModelRepositories
 * @property ExtendedUserModel $model
 * @method ExtendedUserModel newModel()
 */
abstract class ExtendedUserRepository extends DependedRepository implements IUserRepository, IProtectedRepository
{
    use ProtectedRepositoryTrait;

    public function __construct($id = null)
    {
        parent::__construct(SocialLogin::getInstance()->enabled() ? ['user', 'user.socials'] : 'user', $id);
    }

    /**
     * @param ExtendedUserModel|User|IUser|mixed|null $id
     * @return ExtendedUserModel|IUser|mixed|null
     * @throws
     */
    public function model($id = null)
    {
        if ($id instanceof User) {
            $id = $id->getKey();
        }
        return parent::model($id);
    }

    public function queryUniquely($query, $unique)
    {
        return parent::queryUniquely($query, $unique)
            ->orWhereHas('user', function ($query) use ($unique) {
                $query->where('username', $unique)
                    ->orWhere('email', $unique);
            });
    }

    public function createWithAttributes(array $attributes = [], array $userAttributes = [], array $userSocialAttributes = [])
    {
        $userRepository = new UserRepository();
        foreach (['email', 'username'] as $uniqueAttributeName) {
            if (isset($userAttributes[$uniqueAttributeName])) {
                $userRepository->pinModel()
                    ->notStrict()
                    ->withTrashed()
                    ->getUniquely($userAttributes[$uniqueAttributeName]);
                if ($userRepository->hasModel()) {
                    $userRepository->restore();
                }
            }
        }
        if ($userRepository->hasModel()) {
            $userRepository->updateWithAttributes($userAttributes, $userSocialAttributes);
        } else {
            $userRepository->createWithAttributes($userAttributes, $userSocialAttributes);
        }

        $userId = $userRepository->getId();
        $this->pinModel()->notStrict()->withTrashed()->getById($userId);
        if ($this->hasModel()) {
            $this->restore();
            $this->updateWithAttributes($attributes);
        } else {
            $attributes['user_id'] = $userRepository->getId();
            parent::createWithAttributes($attributes);
        }
        return $this->afterCreated();
    }

    public function createWithAttributesFromSocial(array $attributes = [], array $userAttributes = [], array $userSocialAttributes = [])
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
        return $this->afterCreated();
    }

    protected function afterCreated()
    {
        return $this->model;
    }

    public function updateWithAttributes(array $attributes = [], array $userAttributes = [])
    {
        (new UserRepository())->withModel($this->model->user)->updateWithAttributes($userAttributes);
        return parent::updateWithAttributes($attributes);
    }

    public function updateLastAccessedAt()
    {
        return (new UserRepository())
            ->withModel($this->model->user)
            ->updateLastAccessedAt();
    }

    /**
     * @param array $ids
     * @return bool
     * @throws
     */
    public function deleteWithIds(array $ids)
    {
        (new UserRepository())->deleteWithIds($ids);
        return $this->queryDelete(
            $this->dependedWhere(function ($query) {
                $query->noneProtected();
            }, SocialLogin::getInstance()->enabled() ? 'user' : null)
                ->queryByIds($ids)
        );
    }

    public function delete()
    {
        (new UserRepository())->withModel($this->model->user)->delete();
        return parent::delete();
    }
}