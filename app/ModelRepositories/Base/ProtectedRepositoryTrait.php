<?php

namespace App\ModelRepositories\Base;

use App\Exceptions\AppException;
use App\Models\Base\IProtected;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait ProtectedRepositoryTrait
 * @package App\ModelRepositories\Base
 * @property IProtected $model
 */
trait ProtectedRepositoryTrait
{
    protected $protected = true;

    public function getProtectedValues()
    {
        return call_user_func($this->modelClass . '::getProtectedValues');
    }

    public function getProtectedValue()
    {
        return $this->model->getProtectedValue();
    }

    public function skipProtected()
    {
        $this->protected = false;
        return $this;
    }

    protected function onProtected(callable $callback)
    {
        if ($this->protected) {
            $callback();
        }
        $this->protected = true;
    }

    protected function validateProtected($notValidMessage = '')
    {
        $this->onProtected(function () use ($notValidMessage) {
            if (in_array($this->getProtectedValue(), $this->getProtectedValues())) {
                throw new AppException($notValidMessage);
            }
        });
    }

    protected function queryProtected($query)
    {
        if ($this->protected) {
            return $this->queryNoneProtected($query);
        }
        $this->protected = true;
        return $query;
    }

    protected function queryNoneProtected($query)
    {
        return $query->noneProtected();
    }

    /**
     * @return Collection
     * @throws
     */
    public function getNoneProtected()
    {
        return $this->catch(function () {
            return $this->queryNoneProtected($this->query())->get();
        });
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteWithIds(array $ids)
    {
        return $this->queryDelete($this->queryProtected($this->queryByIds($ids)));
    }
}
