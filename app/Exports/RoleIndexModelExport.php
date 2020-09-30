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

    protected $search;

    public function __construct($search = [], $sortBy = null, $sortOrder = 'asc')
    {
        parent::__construct($search, $sortBy, $sortOrder);

        $this->modelRepository = new RoleRepository();
        $this->setModelResourceClass(ExportedRoleResource::class);
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
