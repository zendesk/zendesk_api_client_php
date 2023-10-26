<?php

namespace Zendesk\API\Traits\Utility\Pagination;

use Iterator;

// TODO: doc
// TODO: errors
// TODO: params
// TODO: sorting
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
            // TODO: remove
            echo("\ngetPageIfNecessary NO. " . $this->position);
            return;
        }

        // TODO: don't keep all pages
        $this->page = array_merge($this->page, $this->strategy->getPage());
    }
}
