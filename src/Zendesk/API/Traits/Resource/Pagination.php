<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Traits\Utility\Pagination\AbstractStrategy;
use Zendesk\API\Traits\Utility\Pagination\CbpStrategy;
use Zendesk\API\Traits\Utility\Pagination\PaginationIterator;

trait Pagination {

    /**
     * Usage:
     * foreach ($ticketsIterator as $ticket) {
     *     process($ticket)
     * }
     *
     * @return PaginationIterator to fetch all pages.
     */
    public function iterator()
    {
        $strategyClass = $this->paginationStrategyClass();
        $strategy = new $strategyClass($this, $this->resourcesKey(), AbstractStrategy::DEFAULT_PAGE_SIZE);
        return new PaginationIterator($strategy);
    }

    protected function paginationStrategyClass() {
        return CbpStrategy::class;
    }

    /*
     * @return string eg: "job_statuses"
     */
    protected function resourcesKey() {
        return $this->objectNamePlural;
    }
}
