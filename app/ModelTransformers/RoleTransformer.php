<?php

namespace App\ModelTransformers;

class RoleTransformer extends ModelTransformer
{
    use ModelTransformTrait;

    public function toArray()
    {
        $role = $this->getModel();

        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
            'permissions' => $this->modelTransform(PermissionTransformer::class, $role->permissions),
        ];
    }
}
