<?php

namespace App\ModelRepositories;

use App\Exceptions\Exception;
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
     * @throws Exception
     */
    public function getNoneProtected()
    {
        return $this->catch(function () {
            return $this->query()->noneProtected()->get();
        });
    }
}
