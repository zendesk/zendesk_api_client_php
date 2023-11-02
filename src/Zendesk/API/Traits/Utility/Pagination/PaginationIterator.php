<?php

namespace Zendesk\API\Traits\Utility\Pagination;

use Iterator;

class PaginationIterator implements Iterator
{
    private $position = 0;
    private $page = [];
    private $strategy;
    private $params;

    /**
     * @var mixed use trait FindAll. The object handling the list, Ie: `$client->{clientList}()`
     * Eg: `$client->tickets()` which uses FindAll
     */
    private $clientList;

    public function __construct($clientList, AbstractStrategy $strategy, $params = [])
    {
        $this->clientList = $clientList;
        $this->strategy = $strategy;
        $this->params = $params;
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

        $pageFn = function ($paginationParams = []) {
            return $this->clientList->findAll(
                array_merge(
                    $this->strategy->orderParams($this->params),
                    $paginationParams
                ));
        };

        $this->page = array_merge($this->page, $this->strategy->getPage($pageFn));
    }
}
