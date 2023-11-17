<?php

namespace Zendesk\API\Traits\Utility\Pagination;

/**
 * Offset Based Pagination
 * Used in paginationStrategyClass
 */
class ObpStrategy extends AbstractStrategy
{
    private $pageNumber = 0;

    public function page($getPageFn)
    {
        ++$this->pageNumber;
        $response = $getPageFn();

        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($current_page) {
        return $this->pageNumber == 0 || count($current_page) == 0;
    }
}
