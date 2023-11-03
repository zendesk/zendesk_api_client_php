<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Traits\Utility\Pagination\CbpStrategy;
use Zendesk\API\Traits\Utility\Pagination\PaginationIterator;

trait Pagination {
    /**
     * Usage:
     * $ticketsIterator = $client->tickets()->iterator();
     * foreach ($ticketsIterator as $ticket) {
     *     process($ticket);
     * }
     *
     * @return PaginationIterator to fetch all pages.
     */
    public function iterator($params = [])
    {
        $strategyClass = $this->paginationStrategyClass();
        $strategy = new $strategyClass($this->resourcesKey(), $params);

        return new PaginationIterator($this, $strategy);
    }

    /**
     * Override this method in your resources
     *
     * @return string subclass of AbstractStrategy used for fetching pages
     */
    protected function paginationStrategyClass() {
        return CbpStrategy::class;
    }

    /**
     * @return string eg: "job_statuses"
     */
    protected function resourcesKey() {
        return $this->objectNamePlural;
    }
}
