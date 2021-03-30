<?php

namespace App\ModelCasts;

use App\Exceptions\AppException;
use App\ModelRepositories\Base\ModelRepository;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ModelCast implements CastsAttributes
{
    /**
     * @var ModelRepository
     */
    protected $modelRepository;

    public function __construct($modelRepository)
    {
        $this->modelRepository = ($modelRepository instanceof ModelRepository ? $modelRepository : new $modelRepository())
            ->setModelByUnique();
    }

    /**
     * @param Model|mixed|null $value
     * @return Model|mixed|null
     * @throws
     */
    protected function getValueModel($value)
    {
        return $this->modelRepository->notStrict()->model($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return ($model = $this->getValueModel($value)) ? $model->getKey() : null;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        return $this->getValueModel($value);
    }
}
