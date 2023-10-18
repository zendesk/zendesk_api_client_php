<?php

namespace Zendesk\API\Traits\Utility;

use Iterator;

/**
 * An iterator for fetching resources from the Zendesk API using cursor-based pagination.
 */
class CbpIterator implements Iterator
{
    /**
     * The default number of items per page for pagination.
     */
    public const DEFAULT_PAGE_SIZE = 100;

    /**
     * @var string The root key in the response with the page resources (eg: users, tickets).
     */
    private $resourcesKey;
    /**
     * @var \stdClass implementing the iterator ($this), with findAll() defined.
     */
    private $resourcesRoot;

    /**
     * @var int The current position in the page.
     */
    private $position = 0;

    // TODO: page is actually growing every call, should be only one page (reset position)
    /**
     * @var array The fetched page resources.
     */
    private $page = [];

    /**
     * @var string|null The cursor for the next page of resources.
     */
    private $afterCursor = null;

    /**
     * @var int The number of resources to fetch per page.
     */
    private $pageSize;

    /**
     * @var bool A flag indicating whether the iterator has started fetching resources.
     */
    private $started = false;

    /**
     * @param mixed $resourcesRoot (using trait FindAll)
     * @param int $pageSize The number of resources to fetch per page.
     * @param string $resourcesKey The root key in the response with the page resources (eg: users, tickets)
     */
    public function __construct($resourcesRoot, $resourcesKey, $pageSize = self::DEFAULT_PAGE_SIZE)
    {
        $this->resourcesRoot = $resourcesRoot;
        $this->resourcesKey = $resourcesKey;
        $this->pageSize = $pageSize;
    }

    /**
     * @return int The current position.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Moves to the next resource.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Rewinds to the first resource.
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
        $this->getPageIfNecessary();
        return isset($this->page[$this->position]);
    }

    /**
     * @return mixed (using FindAll) The current resource, maybe fetching a new page.
     */
    public function current()
    {
        $this->getPageIfNecessary();
        return $this->page[$this->position];
    }

    /**
     * Fetches the next page of resources from the API.
     */
    private function getPageIfNecessary()
    {
        if (!$this->isEndOfPage()) {
            return;
        }

        $this->started = true;
        $params = ['page[size]' => $this->pageSize];
        if ($this->afterCursor) {
            $params['page[after]'] = $this->afterCursor;
        }
        $response = $this->resourcesRoot->findAll($params);
        $this->page = array_merge($this->page, $response->{$this->resourcesKey});
        $this->afterCursor = $response->meta->has_more ? $response->meta->after_cursor : null;
    }

    /**
     * @return bool True if the end of the page has been reached, false otherwise.
     */
    private function isEndOfPage()
    {
        return !isset($this->page[$this->position]) && (!$this->started || $this->afterCursor);
    }
}
