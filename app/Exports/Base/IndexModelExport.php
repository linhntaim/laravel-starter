<?php

namespace App\Exports\Base;

use App\Configuration;

abstract class IndexModelExport extends ModelExport
{
    protected $search;
    protected $sortBy;
    protected $sortOrder;

    public function __construct($search = [], $sortBy = null, $sortOrder = 'asc')
    {
        parent::__construct();

        $this->search = $search;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
    }

    protected function query()
    {
        return $this->modelRepository->sort($this->sortBy, $this->sortOrder)
            ->search(
                $this->search,
                Configuration::FETCH_QUERY
            );
    }
}
