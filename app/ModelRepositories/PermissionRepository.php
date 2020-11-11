<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\IProtectedRepository;
use App\ModelRepositories\Base\ModelRepository;
use App\ModelRepositories\Base\ProtectedRepositoryTrait;
use App\Models\Base\IProtected;
use App\Models\Permission;

/**
 * Class PermissionRepository
 * @package App\ModelRepositories
 * @property Permission|IProtected $model
 */
class PermissionRepository extends ModelRepository implements IProtectedRepository
{
    use ProtectedRepositoryTrait;

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

    public function updateWithAttributes(array $attributes = [])
    {
        $this->validateProtected('Cannot edit this protected permission');
        return parent::updateWithAttributes($attributes);
    }

    public function delete()
    {
        $this->validateProtected('Cannot delete this protected permission');

        return parent::delete();
    }
}
