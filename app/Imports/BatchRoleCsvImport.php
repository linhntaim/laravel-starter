<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports;

use App\Imports\Base\BatchModelCsvImport;
use App\ModelRepositories\RoleRepository;

class BatchRoleCsvImport extends BatchModelCsvImport
{
    protected $writeIgnored = true;

    protected function modelRepositoryClass()
    {
        return RoleRepository::class;
    }

    protected function csvHeaders()
    {
        return [
            'name',
            'display_name',
            'description',
        ];
    }

    protected function validatedRules()
    {
        return [
            'name' => 'required|string|max:255|regex:/^[0-9a-z_]+$/',
            'display_name' => 'required|max:255',
        ];
    }
}
