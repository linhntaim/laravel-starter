<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\Exceptions\AppException;
use App\ModelRepositories\Base\ModelRepository;
use App\Models\User;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\SocialLogin;
use App\Utils\StringHelper;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property User $model
 * @method User first($query)
 */
class UserRepository extends ModelRepository
{
    public function modelClass()
    {
        return User::class;
    }

    public function query()
    {
        return SocialLogin::getInstance()->enabled() ?
            parent::query()->with('socials') : parent::query();
    }

    protected function searchOn($query, array $search)
    {
        if (!empty($search['except_protected'])) {
            $query->noneProtected();
        }
        if (!empty($search['email'])) {
            $query->where('email', 'like', '%' . $search['email'] . '%');
        }
        return parent::searchOn($query, $search);
    }

    /**
     * @param string $unique
     * @return User
     * @throws
     */
    public function getUniquely($unique)
    {
        return $this->first(
            $this->query()
                ->where('id', $unique)
                ->orWhere('email', $unique)
        );
    }

    /**
     * @param string $provider
     * @param string $providerId
     * @return User
     * @throws
     */
    public function getSocially($provider, $providerId)
    {
        return SocialLogin::getInstance()->enabled() ? $this->first(
            $this->query()->whereHas('socials', function ($query) use ($provider, $providerId) {
                $query->where('provider', $provider)
                    ->where('provider_id', $providerId);
            })
        ) : null;
    }

    /**
     * @param string $email
     * @return User
     * @throws
     */
    public function getByEmail($email)
    {
        return $this->first(
            $this->query()->where('email', $email)
        );
    }

    /**
     * @param array $attributes
     * @param array $userSocialAttributes
     * @return User
     * @throws
     */
    public function createWithAttributesFromSocial(array $attributes = [], array $userSocialAttributes = [])
    {
        $userSocialRepository = new UserSocialRepository();
        if (!empty($attributes['email']) && ($user = $this->notStrict()->getByEmail($attributes['email']))) {
            $this->model = $user;
            $userSocialAttributes['user_id'] = $this->model->id;
            $userSocialRepository->updateOrCreateWithAttributes($userSocialAttributes);
            $this->updateWithAttributes($attributes);
        } elseif ($userSocial = $userSocialRepository->notStrict()->getByProvider($userSocialAttributes['provider'], $userSocialAttributes['provider_id'])) {
            $this->model = $userSocial->user;
            $this->updateWithAttributes($attributes);
        } else {
            $this->createWithAttributes($attributes, $userSocialAttributes);
        }
        return $this->model;
    }

    /**
     * @param array $attributes
     * @param array $userSocialAttributes
     * @return User
     * @throws
     */
    public function createWithAttributes(array $attributes = [], array $userSocialAttributes = [])
    {
        $socialLogin = SocialLogin::getInstance();
        if (!empty($userSocialAttributes) && isset($attributes['email'])
            && !$socialLogin->checkEmailDomain($attributes['email'])) {
            throw new AppException(static::__transErrorWithModule('email.not_allowed'));
        }
        if (!empty($attributes['password'])) {
            $attributes['password'] = StringHelper::hash($attributes['password']);
            $attributes['password_changed_at'] = DateTimer::syncNow();
        } else {
            unset($attributes['password']);
        }
        parent::createWithAttributes($attributes);
        if ($socialLogin->enabled() && !empty($userSocialAttributes)) {
            $this->model->socials()->create($userSocialAttributes);
        }
        return $this->model;
    }

    /**
     * @param array $attributes
     * @return User
     * @throws
     */
    public function updateWithAttributes(array $attributes = [])
    {
        if (in_array($this->getId(), User::PROTECTED)) {
            throw new AppException('Cannot edit this role');
        }

        if (!empty($attributes['password'])) {
            $attributes['password'] = StringHelper::hash($attributes['password']);
            $attributes['password_changed_at'] = DateTimer::syncNow();
        } else {
            unset($attributes['password']);
        }
        if (empty($attributes['email'])) {
            unset($attributes['email']);
        }
        return parent::updateWithAttributes($attributes);
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteWithIds(array $ids)
    {
        return $this->queryDelete($this->queryByIds($ids)->noneProtected());
    }

    public function delete()
    {
        if (in_array($this->getId(), User::PROTECTED)) {
            throw new AppException('Cannot delete this user');
        }

        return parent::delete();
    }

    public function restoreWithEmail($email)
    {
        $this->withTrashed()
            ->notStrict()
            ->pinModel()
            ->getByEmail($email);
        return $this->restore();
    }

    // TODO: Extra methods

    // TODO
}
