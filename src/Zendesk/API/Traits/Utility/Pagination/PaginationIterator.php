<?php

namespace Zendesk\API\Traits\Utility\Pagination;

const DEFAULT_PAGE_SIZE = 100;

use Iterator;

class PaginationIterator implements Iterator
{
    /**
     * @var mixed using trait FindAll. The object handling the list, Ie: `$client->{clientList}()`
     * Eg: `$client->tickets()` which uses FindAll
     */
    private $clientList;
    private $strategy;
    private $method;
    private $position = 0;
    private $page = [];

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
        if (isset($this->page[$this->position])) {
            return $this->page[$this->position];
        } else {
            return null;
        }
    }

    private function getPageIfNeeded()
    {
        if (!$this->strategy->shouldGetPage($this->position)) {
            return;
        }

        $getPageFn = function () {
            echo("Get page\n");
            return $this->clientList->{$this->method}($this->strategy->params());
        };

        $this->page = array_merge($this->page, $this->strategy->page($getPageFn));
    }
}
