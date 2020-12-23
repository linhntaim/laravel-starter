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
        $this->modelRepository = $modelRepository instanceof ModelRepository ? $modelRepository : new $modelRepository();
    }

    protected function getModel($value)
    {
        return $this->modelRepository->notStrict()->getUniquely($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value instanceof Model) {
            if (get_class($value) == $this->modelRepository->modelClass()) {
                return $value->getKey();
            }
            throw new AppException('Model does not match');
        }
        if ($value = $this->getModel($value)) {
            return $value->getKey();
        }
        return null;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        return $this->getModel($value);
    }
}