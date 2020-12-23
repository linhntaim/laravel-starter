<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface IHasProperties
{
    /**
     * @return array
     */
    public function getPropertyDefinitionConfiguration();

    /**
     * @return PropertyDefinition
     */
    public function getPropertyDefinition();

    /**
     * @return string
     */
    public function getPropertyModelClass();

    /**
     * @return PropertyModel[]|Collection
     */
    public function getProperties();

    /**
     * @return HasMany
     */
    public function properties();
}
