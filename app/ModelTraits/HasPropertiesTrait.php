<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

use App\Models\Base\PropertyDefinition;
use App\Models\Base\PropertyModel;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait PropertyTrait
 * @package App\Models\Base
 * @property array propertyDefinitionConfiguration
 * @property PropertyModel[]|Collection $properties
 */
trait HasPropertiesTrait
{
    /**
     * @return array
     */
    public function getPropertyDefinitionConfiguration()
    {
        return $this->propertyDefinitionConfiguration;
    }

    /**
     * @return PropertyDefinition
     */
    public function getPropertyDefinition()
    {
        return new PropertyDefinition($this->getPropertyDefinitionConfiguration());
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function getPropertiesAttribute()
    {
        $propertyDefinition = $this->getPropertyDefinition();
        $keyedProperties = $this->properties()->get()->keyBy('name');
        $properties = new Collection();
        foreach ($propertyDefinition->getNames() as $name) {
            $properties->put(
                $name,
                tap($keyedProperties->get($name, $this->getNullPropertyInstance($name)), function (PropertyModel $property) {
                    $property->applyValueCaster();
                    return $property;
                })
            );
        }
        return $properties;
    }

    /**
     * @return PropertyModel
     */
    public function getNewPropertyModel()
    {
        $modelClass = $this->getPropertyModelClass();
        return new $modelClass();
    }

    /**
     * @param string $name
     * @return PropertyModel
     */
    protected function getNullPropertyInstance(string $name)
    {
        return call_user_func($this->getPropertyModelClass() . '::nullInstance', $name);
    }

    public function properties()
    {
        return $this->hasMany($this->getPropertyModelClass(), $this->getNewPropertyModel()->getHasPropertyModelForeignKey());
    }
}
