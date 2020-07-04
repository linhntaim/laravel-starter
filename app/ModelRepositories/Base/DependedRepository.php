<?php

namespace App\ModelRepositories\Base;

abstract class DependedRepository extends ModelRepository
{
    protected $depended;
    protected $dependedWhere;
    protected $dependedWith;

    public function __construct($depended, $id = null)
    {
        $this->depended = $depended;

        parent::__construct($id);
    }

    public function dependedWith(callable $callback)
    {
        $this->dependedWith = $callback;
        return $this;
    }

    public function dependedWhere(callable $callback)
    {
        $this->dependedWhere = $callback;
        return $this;
    }

    public function query()
    {
        $query = parent::query();
        if (!is_null($this->dependedWith)) {
            $query->with([
                $this->depended => $this->dependedWith,
            ]);
            $this->dependedWith = null;
        } else {
            $query->with($this->dependedWith);
        }
        if (!is_null($this->dependedWhere)) {
            $query->whereHas($this->depended, $this->dependedWhere);
            $this->dependedWhere = null;
        } else {
            $query->whereHas($this->depended);
        }
        return $query;
    }
}
