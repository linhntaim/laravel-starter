<?php

namespace App\ModelRepositories;

use App\Exceptions\AppException;
use App\ModelRepositories\Base\ModelRepository;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository extends ModelRepository
{
    public function modelClass()
    {
        return Permission::class;
    }

    /**
     * @return Collection
     * @throws
     */
    public function getNoneProtected()
    {
        return $this->catch(function () {
            return $this->query()->noneProtected()->get();
        });
    }

    public function updateWithAttributes(array $attributes = [])
    {
        if (in_array($this->getId(), Permission::PROTECTED)) {
            throw new AppException('Cannot edit this permission');
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
        if (in_array($this->getId(), Permission::PROTECTED)) {
            throw new AppException('Cannot delete this permission');
        }

        return parent::delete();
    }
}
