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
use Illuminate\Support\Facades\DB;

/**
 * Class ExtendedUserRepository
 * @package App\ModelRepositories
 * @property ExtendedUserModel|null $model
 * @method ExtendedUserModel newModel($pinned = true)
 */
abstract class ExtendedUserRepository extends DependedRepository implements IUserRepository
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
                $query->orWhere('email', $unique)
                    ->where(DB::raw('BINARY username'), $unique);
            });
    }

    /**
     * @param array $attributes
     * @param array $userAttributes
     * @param array $userSocialAttributes
     * @return ExtendedUserModel
     * @throws
     */
    public function createWithAttributes(array $attributes = [], array $userAttributes = [], array $userSocialAttributes = [])
    {
        $attributes['user_id'] = (new UserRepository())->createWithAttributes($userAttributes, $userSocialAttributes)->id;
        parent::createWithAttributes($attributes);
        return $this->afterCreated();
    }

    public function createWithUser($user, array $attributes = [])
    {
        $attributes['user_id'] = $this->retrieveId($user);
        parent::createWithAttributes($attributes);
        return $this->afterCreated();
    }

    public function createWithAttributesFromSocial(array $attributes = [], array $userAttributes = [], array $userSocialAttributes = [])
    {
        $userSocialRepository = new UserSocialRepository();
        if (!empty($userAttributes['email']) && ($user = (new UserRepository())->notStrict()->getByEmail($userAttributes['email']))) {
            $userSocialAttributes['user_id'] = $user->id;
            $userSocialRepository->updateOrCreateWithAttributes($userSocialAttributes);
            $this->updateOrCreateWithAttributes(['user_id' => $user->id], $attributes);
        }
        elseif ($userSocial = $userSocialRepository->notStrict()->getByProvider($userSocialAttributes['provider'], $userSocialAttributes['provider_id'])) {
            $this->updateOrCreateWithAttributes(['user_id' => $userSocial->user->id], $attributes);
        }
        else {
            $this->createWithAttributes($attributes, $userAttributes, $userSocialAttributes);
        }
        return $this->afterCreated();
    }

    /**
     * @return ExtendedUserModel
     */
    protected function afterCreated()
    {
        return $this->model;
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return tap(new UserRepository(), function (UserRepository $userRepository) {
            if (!$this->protected) {
                $userRepository->skipProtected();
            }
        });
    }

    public function updateWithAttributes(array $attributes = [], array $userAttributes = [], array $userSocialAttributes = [])
    {
        if (!empty($userAttributes) || !empty($userSocialAttributes)) {
            $this->getUserRepository()
                ->withModel($this->model->user)
                ->updateWithAttributes($userAttributes, $userSocialAttributes);
        }

        $this->validateProtected('Cannot edit this protected user');
        return parent::updateWithAttributes($attributes);
    }

    public function updatePassword($password)
    {
        $this->getUserRepository()
            ->withModel($this->model->user)
            ->updatePassword($password);

        $this->validateProtected('Cannot edit this protected user');
        return $this->model;
    }

    public function updateLastAccessedAt()
    {
        $this->getUserRepository()
            ->withModel($this->model->user)
            ->updateLastAccessedAt();

        $this->validateProtected('Cannot edit this protected user');
        return $this->model;
    }

    /**
     * @param array $ids
     * @return bool
     * @throws
     */
    public function deleteWithIds(array $ids)
    {
        $this->getUserRepository()->deleteWithIds($ids);
        return $this->queryDelete($this->queryProtected($this->queryByIds($ids)));
    }

    public function delete()
    {
        $this->getUserRepository()
            ->withModel($this->model->user)
            ->delete();

        $this->validateProtected('Cannot delete this protected user');
        return parent::delete();
    }
}
