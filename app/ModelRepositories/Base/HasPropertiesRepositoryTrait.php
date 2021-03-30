<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\Models\Base\PropertyModel;

trait HasPropertiesRepositoryTrait
{
    /**
     * @return PropertyRepository
     */
    public function getPropertyRepository()
    {
        $repositoryClass = $this->getPropertyRepositoryClass();
        return new $repositoryClass();
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array $extraPropertyAttributes
     * @return PropertyModel
     */
    public function saveProperty(string $name, $value, $extraPropertyAttributes = [])
    {
        return $this->getPropertyRepository()->save($name, $value, $this->model, $extraPropertyAttributes);
    }

    /**
     * @param array $properties
     * @param array $extraPropertyAttributes
     * @return bool
     */
    public function saveProperties(array $properties, $extraPropertyAttributes = [])
    {
        return $this->getPropertyRepository()->saveMany($properties, $this->model, $extraPropertyAttributes);
    }
}