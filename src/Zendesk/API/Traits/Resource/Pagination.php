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
        $strategy = new $strategyClass($this, $this->resourcesRoot(), AbstractStrategy::DEFAULT_PAGE_SIZE);
        return new PaginationIterator($strategy);
    }

    protected function paginationStrategyClass() {
        return CbpStrategy::class;
    }

    protected function resourcesRoot() {
        return $this->objectNamePlural;
    }
}
