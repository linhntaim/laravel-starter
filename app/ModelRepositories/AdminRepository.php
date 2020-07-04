<?php

namespace App\ModelRepositories;

use App\ModelRepositories\Base\DependedRepository;
use App\ModelRepositories\Base\ModelRepository;
use App\Models\Admin;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property Admin $model
 * @method Admin getById($id, callable $callback = null)
 */
class AdminRepository extends DependedRepository
{
    public function __construct($id = null)
    {
        parent::__construct('user', $id);
    }

    public function modelClass()
    {
        return Admin::class;
    }

    /**
     * @param array $ids
     * @return bool
     * @throws
     */
    public function deleteWithIds(array $ids)
    {
        return $this->queryDelete(
            $this->dependedWhere(function ($query) {
                $query->noneProtected();
            })
                ->queryByIds($ids)
        );
    }
}
