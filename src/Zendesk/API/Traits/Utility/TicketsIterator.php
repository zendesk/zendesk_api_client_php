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
    private $client;

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
     * @param Zendesk\API\HttpClient $client The Zendesk API client.
     * @param int $pageSize The number of tickets to fetch per page.
     */
    public function __construct($client, $pageSize = 2)
    {
        $this->client = $client;
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

    // TODO: expose meta values
    /**
     * Fetches the next page of tickets from the API.
     */
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
