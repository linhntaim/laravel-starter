<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\Exceptions\AppException;
use App\ModelRepositories\UserRepository;
use App\ModelResources\AdminAccountResource;
use App\Models\Admin;
use App\Rules\CurrentPasswordRule;
use App\Utils\SocialLogin;
use Illuminate\Validation\Rule;

class AdminAccountController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new AdminRepository();
    }

    public function index(Request $request)
    {
        $model = $this->modelRepository->notStrict()->getById($request->user()->id);
        if (empty($model)) {
            throw new AppException(static::__transErrorWithModule('not_found'));
        }
        return $this->responseModel(
            $this->setModelResourceClass(AdminAccountResource::class)->modelTransform($model)
        );
    }

    public function store(Request $request)
    {
        $this->modelRepository->model($request->user()->id);

        if ($request->has('_avatar')) {
            return $this->updateAvatar($request);
        }
        if ($request->has('_avatar_by_handled_file')) {
            return $this->updateAvatarByHandledFile($request);
        }
        if ($request->has('_information')) {
            return $this->updateInformation($request);
        }
        if ($request->has('_email')) {
            return $this->updateEmail($request);
        }
        if ($request->has('_password')) {
            return $this->updatePassword($request);
        }

        return $this->responseFail();
    }

    private function updateAvatar(Request $request)
    {
        $this->validated($request, [
            'image' => [
                'required',
                'image',
                sprintf('dimensions:min_width=%d,min_height=%d', Admin::MAX_AVATAR_SIZE, Admin::MAX_AVATAR_SIZE),
            ],
        ]);

        return $this->responseModel(
            $this->modelRepository->updateAvatar($request->file('image'))
        );
    }

    private function updateAvatarByHandledFile(Request $request)
    {
        $this->validated($request, [
            'file_id' => [
                'required',
                'exists:handled_files,id',
            ],
        ]);

        return $this->responseModel(
            $this->modelRepository->updateWithAttributes([
                'avatar_id' => $request->input('file_id'),
            ])
        );
    }

    private function updateInformation(Request $request)
    {
        $this->validated($request, [
            'display_name' => 'required|max:255',
        ]);

        return $this->responseModel(
            $this->modelRepository->updateWithAttributes([
                'display_name' => $request->input('display_name'),
            ])
        );
    }

    private function updateEmail(Request $request)
    {
        $currentUser = $request->user();

        $rules = [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($currentUser->id)->whereNull('deleted_at'),
            ],
        ];
        if (!SocialLogin::getInstance()->enabled() || $currentUser->hasPassword) {
            $rules['current_password'] = [
                'required',
                (new CurrentPasswordRule())
                    ->overrideMessage($this->__transErrorWithModule('current_password.current_password_as_password')),
            ];
        }

        $this->validated($request, $rules);

        return $this->responseModel(
            (new UserRepository($request->user()->id))->updateWithAttributes([
                'email' => $request->input('email'),
            ])
        );
    }

    private function updatePassword(Request $request)
    {
        $currentUser = $request->user();

        $rules = [
            'password' => ['required', 'string', sprintf('min:%d', Admin::MIN_PASSWORD_LENGTH), 'confirmed'],
        ];
        if (!SocialLogin::getInstance()->enabled() || $currentUser->hasPassword) {
            $rules['current_password'] = ['required', new CurrentPasswordRule()];
        }
        $this->validated($request, $rules, [
            'password.confirmed' => $this->__transErrorWithModule('password.confirmed_new'),
        ]);

        return $this->responseModel(
            (new UserRepository($currentUser->id))->updateWithAttributes([
                'password' => $request->input('password'),
            ])
        );
    }
}
