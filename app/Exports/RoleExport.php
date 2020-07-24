<?php

namespace App\Exports;

use App\Configuration;
use App\Exports\Base\ModelExport;
use App\ModelRepositories\RoleRepository;
use App\ModelResources\Base\ModelTransformTrait;
use App\ModelResources\ExportedRoleResource;

class RoleExport extends ModelExport
{
    use ModelTransformTrait;

    const NAME = 'role';

    public function __construct()
    {
        parent::__construct();

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

    protected function query()
    {
        return $this->modelRepository->sort('name')->search([], Configuration::FETCH_QUERY);
    }
}
