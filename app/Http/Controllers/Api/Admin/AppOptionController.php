<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\AppOptionRepository;
use App\ModelTransformers\AppOptionTransformer;

class AppOptionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new AppOptionRepository();
        $this->modelTransformerClass = AppOptionTransformer::class;
    }

    protected function storeValidatedRules(Request $request)
    {
        return [
            'key' => 'required|max:255',
            'value' => 'required',
        ];
    }

    protected function storeExecute(Request $request)
    {
        return $this->modelRepository->save(
            $request->input('key'),
            $request->input('value')
        );
    }

    public function store(Request $request)
    {
        if ($request->has('_many')) {
            return $this->saveMany($request);
        }
        if ($request->has('_app_logo')) {
            return $this->saveAppLogo($request);
        }
        if ($request->has('_info')) {
            return $this->saveInformation($request);
        }
        return parent::store($request);
    }

    private function saveAppLogo(Request $request)
    {
        $this->validated($request, [
            'key' => 'required|max:255',
            'value' => 'required|image|dimensions:min_width=512,min_height=512',
        ]);

        $this->transactionStart();
        return $this->responseModel(
            $this->modelRepository->saveAppLogo($request->file('value'))
        );
    }

    private function saveMany(Request $request)
    {
        $this->validated($request, [
            'options' => 'required',
        ]);

        $options = [];
        foreach ($request->input('options', []) as $key => $value) {
            $options[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        $this->validatedInputs($options, [
            '*.key' => 'required|max:255',
            '*.value' => 'required',
        ]);

        $this->transactionStart();
        $this->modelRepository->saveMany($options);
        return $this->responseModel(
            $this->modelTransform(
                AppOptionTransformer::class,
                (new AppOptionRepository())->getAll()->keyBy('key')
            )
        );
    }

    private function saveInformation(Request $request)
    {
        $this->validated($request, [
            'options' => 'required',
            'options.email' => 'required|string|max:255|email',
            'options.company_name' => 'required|string|max:255',
            'options.company_short_name' => 'required|string|max:255',
            'options.phone' => 'required|regex:/^[0-9-]+$/',
            'options.phone_description' => 'required|string|max:255',
            'options.url' => 'required|url',
        ]);

        $options = [];
        foreach ($request->input('options', []) as $key => $value) {
            $options[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        $this->transactionStart();
        $this->modelRepository->saveMany($options);
        return $this->responseModel(
            $this->modelTransform(
                AppOptionTransformer::class,
                (new AppOptionRepository())->getAll()->keyBy('key')
            )
        );
    }
}
