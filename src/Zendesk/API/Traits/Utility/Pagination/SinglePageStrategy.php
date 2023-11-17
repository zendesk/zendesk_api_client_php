<?php

namespace Zendesk\API\Traits\Utility\Pagination;


/**
 * Single Page (no pagination)
 * Used in paginationStrategyClass
 */
class SinglePageStrategy extends AbstractStrategy
{
    protected $started = false;

    public function page($getPageFn)
    {
        $this->started = true;
        $response = $getPageFn();

        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($current_page) {
        return !$this->started;
    }
}
