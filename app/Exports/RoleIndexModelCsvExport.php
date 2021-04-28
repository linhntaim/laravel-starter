<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports;

use App\Exports\Base\IndexModelCsvExport;
use App\ModelRepositories\RoleRepository;
use App\ModelResources\ExportedRoleResource;

class RoleIndexModelCsvExport extends IndexModelCsvExport
{
    public const NAME = 'role_index';

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
