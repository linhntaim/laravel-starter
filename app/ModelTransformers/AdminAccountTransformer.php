<?php

namespace App\ModelTransformers;

class AdminAccountTransformer extends ModelTransformer
{
    use ModelTransformTrait;

    public function toArray()
    {
        $admin = $this->getModel();

        return array_merge(
            $this->modelTransform(AccountTransformer::class, $admin->user),
            [
                'role_name' => $admin->roleName,
                'permission_names' => $this->modelSafe($admin->permissionNames),
            ]
        );
    }
}
