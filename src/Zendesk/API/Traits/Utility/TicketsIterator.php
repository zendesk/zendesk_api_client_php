<?php

namespace Zendesk\API\Traits\Utility;

use Iterator;

/**
 * An iterator for fetching tickets from the Zendesk API using cursor-based pagination.
 */
class TicketsIterator implements Iterator
{
    /**
     * @var Zendesk\API\HttpClient The Zendesk API client.
     */
    private $resources;

    /**
     * @var int The current position in the tickets array.
     */
    private $position = 0;

    /**
     * @var array The fetched tickets.
     */
    private $tickets = [];

    /**
     * @var string|null The cursor for the next page of tickets.
     */
    private $afterCursor = null;

    /**
     * @var int The number of tickets to fetch per page.
     */
    private $pageSize;

    /**
     * @var bool A flag indicating whether the iterator has started fetching tickets.
     */
    private $started = false;

    // TODO: pageSize = 100
    /**
     * TicketsIterator constructor.
     *
     * @param \stdClass $resources implementing the iterator ($this), with findAll()
     * @param int $pageSize The number of tickets to fetch per page.
     */
    public function __construct($resources, $pageSize = 2)
    {
        $this->resources = $resources;
        $this->pageSize = $pageSize;
    }

    /**
     * @return Ticket The current ticket, possibly fetching a new page.
     */
    public function current()
    {
        if (!isset($this->tickets[$this->position]) && (!$this->started || $this->afterCursor)) {
            $this->getPage();
        }
        return $this->tickets[$this->position];
    }

    /**
     * @return int The current position.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Moves to the next ticket.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Rewinds to the first ticket.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return bool True there is a current element after calls to `rewind` or `next`, possibly fetching a new page.
     */
    public function valid()
    {
        if (!isset($this->tickets[$this->position]) && (!$this->started || $this->afterCursor)) {
            $this->getPage();
        }
        return isset($this->tickets[$this->position]);
    }

    /**
     * Fetches the next page of tickets from the API.
     */
    private function getPage()
    {
        $this->started = true;
        $params = ['page[size]' => $this->pageSize];
        if ($this->afterCursor) {
            $params['page[after]'] = $this->afterCursor;
        }
        $response = $this->resources->findAll($params);
        $this->tickets = array_merge($this->tickets, $response->tickets);
        $this->afterCursor = $response->meta->has_more ? $response->meta->after_cursor : null;
    }
}
