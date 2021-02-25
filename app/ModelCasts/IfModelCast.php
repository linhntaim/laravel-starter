<?php

namespace App\ModelCasts;

class IfModelCast extends ModelCast
{
    protected $valueKey;

    /**
     * @var array|callable|null
     */
    protected $valueAttributes;

    public function __construct($modelRepository, $valueKey = 'value', $valueAttributes = null)
    {
        parent::__construct($modelRepository);

        $this->valueKey = $valueKey;

        $this->valueAttributes = $valueAttributes;
    }

    protected function createValueModel($model, string $key, $value, array $attributes)
    {
        $creatingAttributes = [
            $this->valueKey => $value,
        ];
        if (is_array($this->valueAttributes)) {
            $creatingAttributes = array_merge($creatingAttributes, $this->valueAttributes);
        } elseif (is_callable($this->valueAttributes)) {
            $callback = $this->valueAttributes;
            $creatingAttributes = array_merge($creatingAttributes, $callback($model, $key, $value, $attributes));
        }
        return $this->modelRepository->createWithAttributes($creatingAttributes);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return ($model = $this->getValueModel($value)) ?
            $model->getKey()
            : $this->createValueModel($model, $key, $value, $attributes)->getKey();
    }
}
