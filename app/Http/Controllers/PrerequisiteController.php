<?php

namespace App\Http\Controllers;

use App\Configuration;
use App\Http\Requests\Request;
use App\ModelRepositories\AppOptionRepository;
use App\ModelRepositories\PermissionRepository;
use App\ModelRepositories\RoleRepository;
use App\ModelResources\Base\ModelTransformTrait;
use App\Utils\Framework\ClientLimiter;
use App\Utils\ConfigHelper;
use App\Utils\Framework\ServerMaintainer;
use App\Utils\GuardArrayTrait;
use App\Utils\HandledFiles\Helper;

class PrerequisiteController extends ApiController
{
    use ModelTransformTrait, GuardArrayTrait;

    private $dataset;

    public function __construct()
    {
        parent::__construct();

        $this->dataset = [];
    }

    public function index(Request $request)
    {
        $this->dataset($request);
        return $this->responseSuccess($this->dataset);
    }

    protected function dataset(Request $request)
    {
        $this->server($request);
        $this->roles($request);
        $this->permissions($request);
        $this->locales($request);
    }

    private function server(Request $request)
    {
        if ($request->has('server')) {
            $this->dataset['server'] = [
                'c' => time(),
                'm' => ($serverMaintainer = (new ServerMaintainer())->retrieve()) ? $serverMaintainer->toArray() : null,
                'l' => ($clientLimiter = (new ClientLimiter())->retrieve()) ? $clientLimiter->toArray() : null,
                'url' => url('/'),
                'ips' => $request->ips(),
                'throttle_request' => [
                    'max_attempts' => Configuration::THROTTLE_REQUEST_MAX_ATTEMPTS,
                    'decay_minutes' => Configuration::THROTTLE_REQUEST_DECAY_MINUTES,
                ],
                'max_upload_file_size' => Helper::maxUploadFileSize(),
                'variables' => ConfigHelper::get('variables'),
                'app_options' => $this->guardEmptyAssocArray(
                    $this->modelTransform((new AppOptionRepository())->getAll()->keyBy('key'))
                ),
                'gtm_code' => ConfigHelper::get('gtm_code'),
                'social_login' => [
                    'enabled' => ConfigHelper::isSocialLoginEnabled(),
                ],
                'forgot_password_enabled' => ConfigHelper::get('forgot_password_enabled'),
            ];
        }
    }

    private function roles(Request $request)
    {
        if ($request->has('roles')) {
            $this->dataset['roles'] = $this->modelTransform(
                (new RoleRepository())->getNoneProtected()
            );
        }
    }

    private function permissions(Request $request)
    {
        if ($request->has('permissions')) {
            $this->dataset['permissions'] = $this->modelTransform(
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
