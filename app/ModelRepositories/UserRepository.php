<?php

namespace App\ModelRepositories;

use App\Models\User;
use App\Utils\ConfigHelper;
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

    /**
     * @param int $lengthLowercase
     * @param int $lengthUppercase
     * @param int $lengthNumber
     * @return string
     */
    public static function generatePassword($lengthLowercase = 4, $lengthUppercase = 2, $lengthNumber = 2)
    {
        $lowercaseCharacter = 'abcdefghijklmnopqrstuvwxyz';
        $uppercaseCharacter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numberCharacter = '0123456789';
        $str1 = $lengthLowercase > 0 ? substr(str_shuffle($lowercaseCharacter), 0, $lengthLowercase) : '';
        $str2 = $lengthUppercase > 0 ? substr(str_shuffle($uppercaseCharacter), 0, $lengthUppercase) : '';
        $str3 = $lengthNumber > 0 ? substr(str_shuffle($numberCharacter), 0, $lengthNumber) : '';
        return str_shuffle($str1 . $str2 . $str3);
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
        if (empty($attributes['url_avatar'])) {
            $attributes['url_avatar'] = ConfigHelper::defaultAvatarUrl();
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
            $user->restore();
        }
        return true;
    }
}
