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
     * @return PropertyModel
     */
    public function saveProperty(string $name, $value)
    {
        return $this->getPropertyRepository()->save($name, $value, $this->model);
    }

    /**
     * @param array $properties
     * @return bool
     */
    public function saveProperties(array $properties)
    {
        return $this->getPropertyRepository()->saveMany($properties, $this->model);
    }
}