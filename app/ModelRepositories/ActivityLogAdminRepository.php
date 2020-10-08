<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\Models\ActivityLogAdmin;

/**
 * Class ActivityLogAdminRepository
 * @package App\ModelRepositories
 * @property ActivityLogAdmin $model
 */
class ActivityLogAdminRepository extends ActivityLogRepository
{
    public function modelClass()
    {
        return ActivityLogAdmin::class;
    }

    public function query()
    {
        return parent::query()->with('admin');
    }
}
