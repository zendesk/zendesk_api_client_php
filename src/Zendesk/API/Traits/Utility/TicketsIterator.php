<?php

namespace Zendesk\API\Traits\Utility;

use Iterator;

class TicketsIterator implements Iterator
{
    private $client;
    private $position = 0;
    private $tickets = [];
    private $afterCursor = null;
    private $pageSize;
    private $started = false;

    public function __construct($client, $pageSize = 2)
    {
        $this->client = $client;
        $this->pageSize = $pageSize;
    }

    public function current()
    {
        if (!isset($this->tickets[$this->position]) && (!$this->started || $this->afterCursor)) {
            $this->getPage();
        }
        return $this->tickets[$this->position];
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
        if (!isset($this->tickets[$this->position]) && (!$this->started || $this->afterCursor)) {
            $this->getPage();
        }
        return isset($this->tickets[$this->position]);
    }

    private function getPage()
    {
        $this->started = true;
        if ($this->afterCursor) {
            $params['page[after]'] = $this->afterCursor;
        } else {
            $params = ['page[size]' => $this->pageSize];
        }
        $response = $this->client->tickets()->findAll($params);
        $this->tickets = array_merge($this->tickets, $response->tickets);
        $this->afterCursor = $response->meta->has_more ? $response->meta->after_cursor : null;
    }
}
