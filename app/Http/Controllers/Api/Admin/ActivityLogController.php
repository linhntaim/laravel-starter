<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\ActivityLogAdminRepository;
use App\Utils\ClientSettings\Facade;

class ActivityLogController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new ActivityLogAdminRepository();
    }

    public function search(Request $request)
    {
        $dateTimer = Facade::dateTimer();
        $search = [];
        $input = $request->input('user_id');
        if (!empty($input)) {
            $search['user_id'] = $input;
        }
        $search['client'] = 'admin';
        $input = $request->input('screen');
        if (!empty($input)) {
            $search['screen'] = $input;
        }
        $input = $request->input('action');
        if (!empty($input)) {
            $search['action'] = $input;
        }
        $input = $request->input('created_date_from');
        if (!empty($input)) {
            $input2 = $request->input('created_time_from');
            $search['created_from'] = $dateTimer->fromFormatToDatabaseFormat(
                $dateTimer->compoundFormat('shortDate', ' ', 'longTime'),
                $input . ' ' . (empty($input2) ? '00:00' : $input2) . ':00'
            );
        }
        $input = $request->input('created_date_to');
        if (!empty($input)) {
            $input2 = $request->input('created_time_to');
            $search['created_to'] = $dateTimer->fromFormatToDatabaseFormat(
                $dateTimer->compoundFormat('shortDate', ' ', 'longTime'),
                $input . ' ' . (empty($input2) ? '23:59' : $input2) . ':59'
            );
        }

        // TODO:

        // TODO

        return $search;
    }
}
