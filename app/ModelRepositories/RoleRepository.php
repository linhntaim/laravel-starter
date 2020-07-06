<?php

namespace App\ModelRepositories;

use App\Exceptions\AppException;
use App\ModelRepositories\Base\ModelRepository;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class RoleRepository
 * @package App\ModelRepositories
 * @property Role $model
 */
class RoleRepository extends ModelRepository
{
    public function modelClass()
    {
        return Role::class;
    }

    public function query()
    {
        return parent::query()->with('permissions');
    }

    protected function searchOn($query, array $search)
    {
        if (!empty($search['except_protected'])) {
            $query->noneProtected();
        }
        if (!empty($search['name'])) {
            $query->where('name', 'like', '%' . $search['name'] . '%');
        }
        if (!empty($search['display_name'])) {
            $query->where('display_name', 'like', '%' . $search['display_name'] . '%');
        }
        if (!empty($search['permissions'])) {
            $query->whereHas('permissions', function ($query) use ($search) {
                $query->whereIn('id', $search['permissions']);
            });
        }
        return parent::searchOn($query, $search);
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

    /**
     * @param array $attributes
     * @param array $permissions
     * @return Role
     * @throws
     */
    public function createWithAttributes(array $attributes = [], array $permissions = [])
    {
        $this->createWithAttributes($attributes);
        return $this->catch(function () use ($permissions) {
            if (count($permissions) > 0) {
                $this->model->permissions()->attach($permissions);
            }
            return $this->model;
        });
    }

    /**
     * @param array $attributes
     * @param array $permissions
     * @return Role
     * @throws
     */
    public function updateWithAttributes(array $attributes = [], array $permissions = [])
    {
        if (in_array($this->getId(), Role::PROTECTED)) {
            throw new AppException('Cannot edit this role');
        }

        parent::updateWithAttributes($attributes);
        return $this->catch(function () use ($permissions) {
            if (count($permissions) > 0) {
                $this->model->permissions()->sync($permissions);
            } else {
                $this->model->permissions()->detach();
            }
            return $this->model;
        });
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
        if (in_array($this->getId(), Role::PROTECTED)) {
            throw new AppException('Cannot delete this role');
        }

        return parent::delete();
    }
}
