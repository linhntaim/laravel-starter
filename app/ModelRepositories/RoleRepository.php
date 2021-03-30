<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\IProtectedRepository;
use App\ModelRepositories\Base\ModelRepository;
use App\ModelRepositories\Base\ProtectedRepositoryTrait;
use App\Models\Base\IProtected;
use App\Models\Role;

/**
 * Class RoleRepository
 * @package App\ModelRepositories
 * @property Role|IProtected $model
 */
class RoleRepository extends ModelRepository implements IProtectedRepository
{
    use ProtectedRepositoryTrait;

    public function modelClass()
    {
        return Role::class;
    }

    public function query()
    {
        return parent::query()->with('permissions');
    }

    public function queryUniquely($query, $unique)
    {
        return parent::queryUniquely($query, $unique)
            ->orWhere('name', $unique);
    }

    /**
     * @param string $name
     * @return Role
     */
    public function getByName(string $name)
    {
        return $this->first(
            $this->query()->where('name', $name)
        );
    }

    protected function searchOn($query, array $search)
    {
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
     * @param array $attributes
     * @param array $permissions
     * @return Role
     * @throws
     */
    public function createWithAttributes(array $attributes = [], array $permissions = [])
    {
        parent::createWithAttributes($attributes);
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
        $this->validateProtected('Cannot edit this protected role');

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

    public function delete()
    {
        $this->validateProtected('Cannot delete this protected role');

        return parent::delete();
    }
}
