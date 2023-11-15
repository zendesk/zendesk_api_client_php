<?php

namespace Zendesk\API\Traits\Utility\Pagination;

class PaginationError extends \Exception {}

const DEFAULT_PAGE_SIZE = 100;

use Iterator;

class PaginationIterator implements Iterator
{
    private $clientList;
    private $strategy;
    private $method;
    private $position = 0;
    private $items = [];

    /**
     * @param mixed using trait FindAll. The resources collection, Eg: `$client->tickets()` which uses FindAll
     * @param AbstractStrategy $strategy For pagination Logic (OBP, CBP, SinglePage)
     * @param string $method used to make the API call
     */
    public function __construct($clientList, AbstractStrategy $strategy, $method = 'findAll')
    {
        $this->clientList = $clientList;
        $this->strategy = $strategy;
        $this->method = $method;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        $this->getPageIfNeeded();
        return !!$this->current();
    }

    public function current()
    {
        if (isset($this->items[$this->position])) {
            return $this->items[$this->position];
        } else {
            return null;
        }
    }

    /**
     * Returns the latest HTTP response, unless an error occurred, which causes an exception
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function latestResponse()
    {
        return $this->strategy->latestResponse();
    }
    private function getPageIfNeeded()
    {
        if (isset($this->items[$this->position]) || !$this->strategy->shouldGetPage($this->position)) {
            return;
        }

        $getPageFn = function () {
            return $this->clientList->{$this->method}($this->strategy->params());
        };

        $this->items = array_merge($this->items, $this->strategy->page($getPageFn));
    }
}
