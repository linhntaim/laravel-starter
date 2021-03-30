<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exports;

use App\Configuration;
use App\Exports\Base\ModelExport;
use App\ModelRepositories\RoleRepository;
use App\ModelResources\ExportedRoleResource;

class RoleModelExport extends ModelExport
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

    protected function query()
    {
        // get all sorted by name ascending
        return $this->modelRepository->sort('name')
            ->search([], Configuration::FETCH_QUERY);
    }
}
