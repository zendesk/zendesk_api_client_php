<?php

namespace Zendesk\API\Traits\Utility\Pagination;


/**
 * Single Page (no pagination)
 * Used in paginationStrategyClass
 */
class SinglePageStrategy extends AbstractStrategy
{
    protected $started = false;

    public function getPage($pageFn)
    {
        $this->started = true;
        $response = $pageFn();

        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        return !$this->started;
    }
}
