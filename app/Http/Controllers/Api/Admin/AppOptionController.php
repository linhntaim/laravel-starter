<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\AppOptionRepository;
use App\ModelTransformers\AppOptionTransformer;

class AppOptionController extends ModelApiController
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
        return parent::store($request);
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

        $this->validatedData($options, [
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
}
