<?php

namespace Zendesk\API\Traits\Utility;

use Iterator;

/**
 * An iterator for fetching tickets from the Zendesk API using cursor-based pagination.
 */
class TicketsIterator implements Iterator
{
    /**
     * The default number of items per page for pagination.
     */
    public const DEFAULT_PAGE_SIZE = 100;

    /**
     * @var Zendesk\API\HttpClient The Zendesk API client.
     */
    private $resourcesRoot;

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

    /**
     * TicketsIterator constructor.
     *
     * @param \stdClass $resourcesRoot implementing the iterator ($this), with findAll() defined
     * @param int $pageSize The number of tickets to fetch per page.
     */
    public function __construct($resourcesRoot, $pageSize = self::DEFAULT_PAGE_SIZE)
    {
        $this->resourcesRoot = $resourcesRoot;
        $this->pageSize = $pageSize;
    }

    /**
     * @return Ticket The current ticket, possibly fetching a new page.
     */
    public function current()
    {
        if ($this->isEndOfPage()) {
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
        if ($this->isEndOfPage()) {
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
        $response = $this->resourcesRoot->findAll($params);
        $this->tickets = array_merge($this->tickets, $response->tickets);
        $this->afterCursor = $response->meta->has_more ? $response->meta->after_cursor : null;
    }

    /**
     * @return bool True if the end of the page has been reached, false otherwise.
     */
    private function isEndOfPage()
    {
        return !isset($this->tickets[$this->position]) && (!$this->started || $this->afterCursor);
    }
}
