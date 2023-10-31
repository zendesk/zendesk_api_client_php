<?php

namespace Zendesk\API\Traits\Utility\Pagination;

use Iterator;

class PaginationIterator implements Iterator
{
    private $position = 0;
    private $page = [];
    private $strategy;

    public function __construct(AbstractStrategy $strategy)
    {
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
        $this->getPageIfNecessary();
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

    private function getPageIfNecessary()
    {
        if (!$this->strategy->shouldGetPage($this->position)) {
            return;
        }

        $this->page = array_merge($this->page, $this->strategy->getPage());
    }
}
