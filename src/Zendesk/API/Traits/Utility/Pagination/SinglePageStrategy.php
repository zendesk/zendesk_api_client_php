<?php

namespace Zendesk\API\Traits\Utility\Pagination;


/**
 * Single Page (no pagination)
 */
class SinglePageStrategy extends PaginationStrategy
{
    protected $started = false;

    public function getPage()
    {
        $this->started = true;
        $response = $this->resourcesRoot->findAll();

        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        return !$this->started;
    }
}
