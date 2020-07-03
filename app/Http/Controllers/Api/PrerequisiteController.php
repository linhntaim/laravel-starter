<?php

namespace App\Http\Controllers\Api;

use App\Configuration;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\CheckForClientLimitation;
use App\Http\Requests\Request;
use App\ModelRepositories\AppOptionRepository;
use App\ModelRepositories\PermissionRepository;
use App\ModelRepositories\RoleRepository;
use App\ModelTransformers\AppOptionTransformer;
use App\ModelTransformers\PermissionTransformer;
use App\ModelTransformers\RoleTransformer;
use App\ModelTransformers\ModelTransformTrait;
use App\Utils\ConfigHelper;
use App\Utils\Files\FileHelper;

class PrerequisiteController extends ApiController
{
    use ModelTransformTrait;

    private $dataset;

    public function __construct()
    {
        parent::__construct();

        $this->dataset = [];
    }

    public function index(Request $request)
    {
        $this->server($request);
        $this->roles($request);
        $this->permissions($request);
        $this->locales($request);
        return $this->responseSuccess($this->dataset);
    }

    private function server(Request $request)
    {
        if ($request->has('server')) {
            $this->dataset['server'] = [
                'c' => time(),
                'm' => app()->isDownForMaintenance() ?
                    json_decode(file_get_contents(storage_path('framework/down')), true) : null,
                'l' => CheckForClientLimitation::hasLimitation() ?
                    CheckForClientLimitation::limitation() : null,
                'url' => url('/'),
                'ips' => $request->ips(),
                'throttle_request' => [
                    'max_attempts' => Configuration::THROTTLE_REQUEST_MAX_ATTEMPTS,
                    'decay_minutes' => Configuration::THROTTLE_REQUEST_DECAY_MINUTES,
                ],
                'max_upload_file_size' => FileHelper::getInstance()->maxUploadFileSize(),
                'variables' => ConfigHelper::get('variables'),
                'app_options' => $this->modelSafe(
                    $this->modelTransform(
                        AppOptionTransformer::class,
                        (new AppOptionRepository())->getAll()->keyBy('key')
                    )
                ),
                'gtm_code' => ConfigHelper::get('gtm_code'),
            ];
        }
    }

    private function roles(Request $request)
    {
        if ($request->has('roles')) {
            $this->dataset['roles'] = $this->modelTransform(
                RoleTransformer::class,
                (new RoleRepository())->getNoneProtected()
            );
        }
    }

    private function permissions(Request $request)
    {
        if ($request->has('permissions')) {
            $this->dataset['permissions'] = $this->modelTransform(
                PermissionTransformer::class,
                (new PermissionRepository())->getNoneProtected()
            );
        }
    }

    private function locales(Request $request)
    {
        if ($request->has('locales')) {
            $locales = [];
            foreach (ConfigHelper::getLocaleCodes() as $code) {
                $locales[] = [
                    'code' => $code,
                    'name' => trans('locale.' . $code),
                ];
            }
            $this->dataset['locales'] = $locales;
        }
    }
}
