<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\ActivityLogAdminRepository;
use App\Utils\ClientSettings\Facade;

/**
 * Class ActivityLogController
 * @package App\Http\Controllers\Api\Admin
 * @property ActivityLogAdminRepository $modelRepository
 */
class ActivityLogController extends ModelApiController
{
    protected $sortByAllows = [
        'created_at',
        'screen',
        'action',
    ];

    protected function modelRepositoryClass()
    {
        return ActivityLogAdminRepository::class;
    }

    protected function searchParams(Request $request)
    {
        $dateTimer = Facade::dateTimer();
        return [
            'user_id',
            'screen',
            'action',
            'created_date_from' => [
                'created_from',
                function ($input, Request $request) use ($dateTimer) {
                    $input2 = $request->input('created_time_from');
                    return $dateTimer->fromFormatToDatabaseFormat(
                        $dateTimer->compoundFormat('shortDate', ' ', 'longTime'),
                        $input . ' ' . (empty($input2) ? '00:00' : $input2) . ':00'
                    );
                },
            ],
            'created_date_to' => [
                'created_to',
                function ($input, Request $request) use ($dateTimer) {
                    $input2 = $request->input('created_time_to');
                    return $dateTimer->fromFormatToDatabaseFormat(
                        $dateTimer->compoundFormat('shortDate', ' ', 'longTime'),
                        $input . ' ' . (empty($input2) ? '23:59' : $input2) . ':59'
                    );
                },
            ],
            // TODO:

            // TODO
        ];
    }

    protected function searchDefaultParams(Request $request)
    {
        return [
            'client' => 'admin',
            // TODO:

            // TODO
        ];
    }
}
