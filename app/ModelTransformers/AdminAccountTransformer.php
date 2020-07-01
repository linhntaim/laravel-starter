<?php

namespace App\ModelTransformers;

use App\Models\Admin;

/**
 * Class AdminAccountTransformer
 * @package App\ModelTransformers
 * @method Admin getModel()
 */
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
                'display_name' => $admin->display_name,
                'avatar_url' => $admin->avatarUrl,
            ]
        );
    }
}
