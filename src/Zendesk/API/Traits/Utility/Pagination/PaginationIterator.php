<?php

namespace Zendesk\API\Traits\Utility\Pagination;

use Iterator;

class PaginationIterator implements Iterator
{
    private $position = 0;
    private $page = [];
    private $strategy;
    /**
     * @var mixed use trait FindAll. The object handling the list, Ie: `$client->{clientList}()`
     */
    private $clientList;

    public function __construct($clientList, AbstractStrategy $strategy)
    {
        $this->clientList = $clientList;
        $this->strategy = $strategy;
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

        $pageFn = function ($params = []) {
            return $this->clientList->findAll($params);
        };

        $this->page = array_merge($this->page, $this->strategy->getPage($pageFn));
    }
}
