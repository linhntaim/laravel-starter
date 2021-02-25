<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Imports;

use App\Imports\Base\ModelCsvImport;
use App\ModelRepositories\RoleRepository;

class RoleCsvImport extends ModelCsvImport
{
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

    protected function modelImporting($read, $counter)
    {
        return $this->modelRepository->updateOrCreateWithAttributes(['name' => $read['name']], $read);
    }
}
