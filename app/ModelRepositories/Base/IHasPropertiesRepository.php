<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\Models\Base\PropertyModel;

interface IHasPropertiesRepository
{
    /**
     * @param string $name
     * @param mixed $value
     * @return PropertyModel
     */
    public function saveProperty(string $name, $value);

    /**
     * @param array $properties
     * @return boolean
     */
    public function saveProperties(array $properties);

    /**
     * @return string
     */
    public function getPropertyRepositoryClass();

    /**
     * @return PropertyRepository
     */
    public function getPropertyRepository();
}