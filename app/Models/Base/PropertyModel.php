<?php

namespace App\Models\Base;

use App\ModelCasts\SelfCast;
use App\ModelResources\Base\PropertyResource;

/**
 * Class PropertyModel
 * @package App\Models\Base
 * @property string $name
 * @property mixed $value
 */
abstract class PropertyModel extends Model implements ICaster
{
    public $timestamps = false;

    protected $visible = [
        'name',
    ];

    protected $casts = [
        'value' => SelfCast::class,
    ];

    protected $resourceClass = PropertyResource::class;

    public function getCaster(string $key, array $attributes)
    {
        return $this->getPropertyDefinition()->getCast($attributes['name']);
    }

    public static function nullInstance(string $name)
    {
        $model = new static();
        $model->name = $name;
        return $model;
    }

    /**
     * @return PropertyDefinition
     */
    public function getPropertyDefinition()
    {
        return $this->getNewHasPropertyModel()->getPropertyDefinition();
    }

    /**
     * @return IHasProperties
     */
    public function getNewHasPropertyModel()
    {
        $modelClass = $this->getHasPropertyModelClass();
        return new $modelClass();
    }

    public abstract function getHasPropertyModelClass();

    public abstract function getHasPropertyModelForeignKey();

    public function hasPropertyModel()
    {
        return $this->belongsTo($this->getHasPropertyModelClass(), $this->getHasPropertyModelForeignKey());
    }
}
