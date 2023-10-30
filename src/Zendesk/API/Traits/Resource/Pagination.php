<?php

namespace Zendesk\API\Traits\Resource;

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
        $strategy = new $strategyClass($this, $this->resourcesRoot(), $this->defaultPageSize());
        return new PaginationIterator($strategy);
    }

    private function defaultPageSize()
    {
        return 100;
    }

    private function paginationStrategyClass() {
        return CbpStrategy::class;
    }

    protected function resourcesRoot() {
        return $this->objectNamePlural;
    }
}
