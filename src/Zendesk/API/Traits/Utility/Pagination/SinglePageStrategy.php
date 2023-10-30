<?php

namespace Zendesk\API\Traits\Utility\Pagination;


/**
 * Single Page (no pagination)
 */
class SinglePageStrategy extends AbstractStrategy
{
    protected $started = false;

    public function getPage()
    {
        $this->started = true;
        $response = $this->clientResources->findAll();

        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        return !$this->started;
    }
}
