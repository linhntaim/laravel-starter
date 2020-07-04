<?php

namespace App\ModelRepositories;

use App\Exceptions\AppException;
use App\ModelRepositories\Base\ModelRepository;
use App\Models\User;
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
}
