<?php

namespace App\ModelRepositories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class UserRepository
 * @package App\ModelRepositories
 * @property Admin $model
 * @method Admin getById($id, $strict = true)
 */
class AdminRepository extends ModelRepository
{
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
        return $this->catch(function () use ($ids) {
            $this->queryByIds($ids)->whereHas('user', function (Builder $query) {
                $query->noneProtected();
            })->delete();
            return true;
        });
    }
}
