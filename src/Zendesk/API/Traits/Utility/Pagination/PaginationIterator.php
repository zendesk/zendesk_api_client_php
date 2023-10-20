<?php

namespace Zendesk\API\Traits\Utility\Pagination;

use Iterator;

class PaginationIterator implements Iterator
{
    private $position = 0;
    private $page = [];
    private $strategy;

    public function __construct(PaginationStrategy $strategy)
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
        return isset($this->page[$this->position]);
    }

    public function current()
    {
        $this->getPageIfNecessary();
        return $this->page[$this->position];
    }

    private function getPageIfNecessary()
    {
        if (!$this->isEndOfPage()) {
            return;
        }

        $this->page = array_merge($this->page, $this->strategy->getPage());
    }

    private function isEndOfPage() {
        return !isset($this->page[$this->position]) && $this->strategy->isEndOfPage();
    }
}
