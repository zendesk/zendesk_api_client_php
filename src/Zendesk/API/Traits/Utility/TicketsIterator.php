<?php

namespace Zendesk\API\Traits\Utility;

use Iterator;

class TicketsIterator implements Iterator
{
    const DEFAULT_PAGE_SIZE = 100;
    private $client;
    private $position = 0;
    private $tickets = [];
    private $nextCursor = null;

    public function __construct($client)
    {
        $this->client = $client;
        $this->loadTickets();
    }

    public function current()
    {
        return $this->tickets[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
        if (!isset($this->tickets[$this->position]) && $this->nextCursor) {
            $this->loadTickets();
        }
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->tickets[$this->position]);
    }

    private function loadTickets()
    {
        // TODO: store all meta info
        $params = ['page[size]' => self::DEFAULT_PAGE_SIZE];
        if ($this->nextCursor) {
            $params['page[after]'] = $this->nextCursor;
        }
        $response = $this->client->tickets()->findAll($params);
        $this->tickets = array_merge($this->tickets, $response->tickets);
        $this->nextCursor = $response->meta->has_more ? $response->meta->links->next : null;
    }
}
