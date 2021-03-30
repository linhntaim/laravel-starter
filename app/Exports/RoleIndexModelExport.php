<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports;

use App\Exports\Base\IndexModelExport;
use App\ModelRepositories\RoleRepository;
use App\ModelResources\ExportedRoleResource;

class RoleIndexModelExport extends IndexModelExport
{
    const NAME = 'role';

    protected function modelRepositoryClass()
    {
        return RoleRepository::class;
    }

    protected function modelResourceClass()
    {
        return ExportedRoleResource::class;
    }

    public function csvHeaders()
    {
        return [
            'ID',
            'Name',
            'Display',
            'Description',
        ];
    }
}
