<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use Illuminate\Support\Str;

abstract class DependedRepository extends ModelRepository
{
    /**
     * @var array
     */
    protected $depended;

    /**
     * @var callable[]|array
     */
    protected $dependedWhere;

    /**
     * @var callable[]|array
     */
    protected $dependedWith;

    public function __construct($depended, $id = null)
    {
        $this->depended = (array)$depended;
        $this->dependedWith = [];
        $this->dependedWhere = [];

        parent::__construct($id);
    }

    public function dependedWith(callable $callback, $depended = null)
    {
        if (is_null($depended)) {
            $depended = $this->depended[0];
        }
        $this->dependedWith[$depended] = $callback;
        return $this;
    }

    public function dependedWhere(callable $callback, $depended = null)
    {
        if (is_null($depended)) {
            $depended = $this->depended[0];
        }
        $this->dependedWhere[$depended] = $callback;
        return $this;
    }

    public function query()
    {
        $query = parent::query();
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
        return $query;
    }
}
