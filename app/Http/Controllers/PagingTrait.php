<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers;

use App\Configuration;

trait PagingTrait
{
    protected $sortByAllows = [];

    protected $sortBy = null;

    protected $sortOrder = 'asc';

    protected $moreByAllows = [];

    protected $moreBy = null;

    protected $moreOrder = 'asc';

    protected function paging()
    {
        return $this->paged() ? Configuration::FETCH_PAGING_YES : Configuration::FETCH_PAGING_NO;
    }

    protected function paged()
    {
        return request()->has('page');
    }

    protected function itemsPerPage()
    {
        if (request()->has('fixed_per_page')) {
            return request()->input('fixed_per_page');
        }

        $itemsPerPage = request()->input('items_per_page', Configuration::DEFAULT_ITEMS_PER_PAGE);
        return in_array($itemsPerPage, Configuration::ALLOWED_ITEMS_PER_PAGE) ?
            $itemsPerPage : Configuration::DEFAULT_ITEMS_PER_PAGE;
    }

    protected function sortBy()
    {
        $sortBy = request()->input('sort_by', $this->sortBy);
        return empty($this->sortByAllows) || in_array($sortBy, $this->sortByAllows, true) ?
            $sortBy : $this->sortBy;
    }

    protected function sortOrder()
    {
        $sortOrder = strtolower(request()->input('sort_order', $this->sortOrder));
        return in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';
    }

    protected function moreBy()
    {
        $moreBy = request()->input('more_by', $this->moreBy);
        return empty($this->moreByAllows) || in_array($moreBy, $this->moreByAllows, true) ?
            $moreBy : $this->moreBy;
    }

    protected function moreOrder()
    {
        $moreOrder = strtolower(request()->input('more_order', $this->moreOrder));
        return in_array($moreOrder, ['asc', 'desc']) ? $moreOrder : 'asc';
    }

    protected function morePivot()
    {
        return request()->input('more_pivot');
    }
}
