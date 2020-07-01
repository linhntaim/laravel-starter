<?php

namespace App\ModelRepositories;

use App\Exceptions\AppException;
use App\Models\User;
use App\Utils\StringHelper;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property User $model
 */
class UserRepository extends ModelRepository
{
    public function modelClass()
    {
        return User::class;
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
     * @param string $email
     * @param bool $strict
     * @param null|bool $lock
     * @return User
     * @throws
     */
    public function getByEmail($email, $strict = true, $lock = null)
    {
        return $this->catch(function () use ($email, $strict, $lock) {
            return $strict ?
                $this->query()
                    ->where('email', $email)
                    ->lock($lock)
                    ->firstOrFail()
                : $this->query()
                    ->where('email', $email)
                    ->lock($lock)
                    ->first();
        });
    }

    public function getByEmailWithTrashed($email, $strict = true)
    {
        return $strict ?
            $this->query()->withTrashed()->where('email', $email)->firstOrFail()
            : $this->query()->withTrashed()->where('email', $email)->first();
    }

    public function restoreTrashedEmail($email)
    {
        $user = $this->getByEmailWithTrashed($email, false);
        if ($user && $user->trashed()) {
            return $this->catch(function () use ($user) {
                $user->restore();
                return $user;
            });
        }
        return $user;
    }

    /**
     * @param array $attributes
     * @return User
     * @throws
     */
    public function createWithAttributes(array $attributes = [])
    {
        if (!empty($attributes['password'])) {
            $attributes['password'] = StringHelper::hash($attributes['password']);
        } else {
            unset($attributes['password']);
        }
        return parent::createWithAttributes($attributes);
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
     * @throws
     */
    public function deleteWithIds(array $ids)
    {
        return $this->catch(function () use ($ids) {
            $this->queryByIds($ids)->noneProtected()->delete();
            return true;
        });
    }

    public function delete()
    {
        if (in_array($this->getId(), User::PROTECTED)) {
            throw new AppException('Cannot delete this user');
        }

        return parent::delete();
    }
}
