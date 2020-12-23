<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\Models\Base\IHasProperties;
use App\Models\Base\Model;
use App\Models\Base\PropertyModel;

/**
 * Class PropertyRepository
 * @package App\ModelRepositories\Base
 * @method PropertyModel newModel()
 */
abstract class PropertyRepository extends ModelRepository
{
    public function getPropertyDefinition()
    {
        return $this->newModel()->getPropertyDefinition();
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param IHasProperties|Model $hasPropertyModel
     * @return PropertyModel
     * @throws
     */
    public function save(string $name, $value, $hasPropertyModel)
    {
        $newModel = $this->newModel();
        $newModel->name = $name;
        $hasPropertyModelForeignKey = $newModel->getHasPropertyModelForeignKey();
        return $this->useModelQuery($newModel)
            ->updateOrCreateWithAttributes([
                $hasPropertyModelForeignKey => $this->retrieveId($hasPropertyModel),
                'name' => $name,
            ], [
                'value' => $value,
            ]);
    }

    /**
     * @param array $properties
     * @param IHasProperties|Model $hasPropertyModel
     * @return boolean
     * @throws
     */
    public function saveMany(array $properties, $hasPropertyModel)
    {
        foreach ($properties as $name => $value) {
            $this->save($name, $value, $hasPropertyModel);
        }
        return true;
    }
}
