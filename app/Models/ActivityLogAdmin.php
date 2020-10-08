<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\ModelResources\ActivityLogAdminResource;

class ActivityLogAdmin extends ActivityLog
{
    protected $resourceClass = ActivityLogAdminResource::class;

    public function getLoginLogAttribute()
    {
        return trans('activity_log.login', [
            'log' => $this->listedWithModel($this->admin, '<br>', '- '),
        ]);
    }

    public function getLogoutLogAttribute()
    {
        return trans('activity_log.logout', [
            'log' => $this->listedWithModel($this->admin, '<br>', '- '),
        ]);
    }
}