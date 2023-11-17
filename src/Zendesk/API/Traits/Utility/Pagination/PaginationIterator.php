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
    private $page = [];

    /**
     * @param mixed using trait FindAll. The resources collection, Eg: `$client->tickets()` which uses FindAll
     * @param AbstractStrategy $strategy For pagination Logic (OBP, CBP, SinglePage)
     * @param string $method used to make the API call
     */
    public function __construct($clientList, AbstractStrategy $strategy, $method)
    {
        $this->clientList = $clientList;
        $this->strategy = $strategy;
        $this->method = $method;
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        ++$this->position;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        $this->getPageIfNeeded();
        return !!$this->current();
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        if (isset($this->page[$this->position])) {
            return $this->page[$this->position];
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
        if (isset($this->page[$this->position]) || !$this->strategy->shouldGetPage($this->page)) {
            return;
        }

        $getPageFn = function () {
            return $this->clientList->{$this->method}($this->strategy->params());
        };
        $this->page = $this->strategy->page($getPageFn);
        $this->position = 0;
    }
}
