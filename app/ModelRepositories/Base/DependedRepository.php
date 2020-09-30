<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use Closure;
use Illuminate\Support\Str;

abstract class DependedRepository extends ModelRepository
{
    /**
     * @var string|array
     */
    protected $depended;

    /**
     * @var callable|Closure|array
     */
    protected $dependedWhere;

    /**
     * @var callable|Closure|array
     */
    protected $dependedWith;

    public function __construct($depended, $id = null)
    {
        $this->depended = $depended;
        if (is_array($this->depended)) {
            $this->dependedWith = [];
            $this->dependedWhere = [];
        }

        parent::__construct($id);
    }

    public function dependedWith(callable $callback, $depended = null)
    {
        if (is_array($this->dependedWith) && !is_null($depended)) {
            $this->dependedWith[$depended] = $callback;
        } else {
            $this->dependedWith = $callback;
        }
        return $this;
    }

    public function dependedWhere(callable $callback, $depended = null)
    {
        if (is_array($this->dependedWhere) && !is_null($depended)) {
            $this->dependedWhere[$depended] = $callback;
        } else {
            $this->dependedWhere = $callback;
        }
        return $this;
    }

    public function query()
    {
        $query = parent::query();
        if (is_array($this->depended)) {
            foreach ($this->depended as $depended) {
                if (isset($this->dependedWith[$depended])) {
                    $query->with([
                        $depended => $this->dependedWith[$depended],
                    ]);
                } else {
                    $query->with($depended);
                }
                if (isset($this->dependedWhere[$depended])) {
                    $query->whereHas($depended, $this->dependedWhere[$depended]);
                } else {
                    if (!Str::contains($depended, '.')) {
                        $query->whereHas($depended);
                    }
                }
            }
            $this->dependedWith = [];
            $this->dependedWhere = [];
        } else {
            if (!is_null($this->dependedWith)) {
                $query->with([
                    $this->depended => $this->dependedWith,
                ]);
                $this->dependedWith = null;
            } else {
                $query->with($this->depended);
            }
            if (!is_null($this->dependedWhere)) {
                $query->whereHas($this->depended, $this->dependedWhere);
                $this->dependedWhere = null;
            } else {
                $query->whereHas($this->depended);
            }
        }
        return $query;
    }
}
