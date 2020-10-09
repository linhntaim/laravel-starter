<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\Exceptions\AppException;
use App\ModelRepositories\Base\ModelRepository;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PermissionRepository
 * @package App\ModelRepositories
 * @property Permission $model
 */
class PermissionRepository extends ModelRepository
{
    public function modelClass()
    {
        return Permission::class;
    }

    /**
     * @param string $name
     * @return Permission
     */
    public function getByName(string $name)
    {
        return $this->first(
            $this->query()->where('name', $name)
        );
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
        if (in_array($this->model->name, Permission::PROTECTED)) {
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
        if (in_array($this->model->name, Permission::PROTECTED)) {
            throw new AppException('Cannot delete this permission');
        }

        return parent::delete();
    }
}
