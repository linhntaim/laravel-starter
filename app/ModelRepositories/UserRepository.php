<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\Exceptions\AppException;
use App\ModelRepositories\Base\IProtectedRepository;
use App\ModelRepositories\Base\IUserRepository;
use App\ModelRepositories\Base\ModelRepository;
use App\ModelRepositories\Base\ProtectedRepositoryTrait;
use App\Models\Base\IProtected;
use App\Models\User;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\SocialLogin;
use App\Vendors\Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property User|IProtected $model
 * @method User|null getUniquely($unique)
 */
class UserRepository extends ModelRepository implements IProtectedRepository, IUserRepository
{
    use ProtectedRepositoryTrait;

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
        if (!empty($search['email'])) {
            $query->where('email', 'like', '%' . $search['email'] . '%');
        }
        return parent::searchOn($query, $search);
    }

    public function queryUniquely($query, $unique)
    {
        return parent::queryUniquely($query, $unique)
            ->orWhere('email', $unique)
            ->orWhere(DB::raw('BINARY username'), $unique);
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
     * @param string $username
     * @return User
     * @throws
     */
    public function getByUsername($username)
    {
        return $this->first(
            $this->query()->where('username', $username)
        );
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
            $attributes['password'] = Str::hash($attributes['password']);
            $attributes['password_changed_at'] = DateTimer::syncNow();
        } else {
            unset($attributes['password']);
        }
        parent::createWithAttributes($attributes);
        if ($socialLogin->enabled() && !empty($userSocialAttributes)) {
            $userSocialAttributes['user_id'] = $this->model->id;
            (new UserSocialRepository())->createWithAttributes($userSocialAttributes);
        }
        return $this->model;
    }

    /**
     * @param array $attributes
     * @param array $userSocialAttributes
     * @return User
     * @throws
     */
    public function updateWithAttributes(array $attributes = [], array $userSocialAttributes = [])
    {
        $this->validateProtected('Cannot edit this protected user');

        $socialLogin = SocialLogin::getInstance();
        if (!empty($userSocialAttributes) && isset($attributes['email'])
            && !$socialLogin->checkEmailDomain($attributes['email'])) {
            throw new AppException(static::__transErrorWithModule('email.not_allowed'));
        }

        if (!empty($attributes['password'])) {
            $attributes['password'] = Str::hash($attributes['password']);
            $attributes['password_changed_at'] = DateTimer::syncNow();
        } else {
            unset($attributes['password']);
        }
        if (empty($attributes['email'])) {
            unset($attributes['email']);
        }
        parent::updateWithAttributes($attributes);
        if ($socialLogin->enabled() && !empty($userSocialAttributes)) {
            $userSocialAttributes['user_id'] = $this->model->id;
            (new UserSocialRepository())->updateOrCreateWithAttributes($userSocialAttributes);
        }
        return $this->model;
    }

    public function updateLastAccessedAt()
    {
        return $this->skipProtected()->updateWithAttributes([
            'last_accessed_at' => DateTimer::syncNow(),
        ]);
    }

    public function delete()
    {
        $this->validateProtected('Cannot delete this protected user');

        return parent::delete();
    }

    public function restoreWithEmail($email)
    {
        $this->withTrashed()
            ->notStrict()
            ->pinModel()
            ->getByEmail($email);
        return $this->hasModel() ? $this->restore() : null;
    }

    // TODO: Extra methods

    // TODO
}
