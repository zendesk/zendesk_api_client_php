<?php

namespace Zendesk\API\Traits\Utility\Pagination;

/**
 * Offset Based Pagination
 * Used in paginationStrategyClass
 */
class ObpStrategy extends AbstractStrategy
{
    private $pageNumber = 0;

    public function getPage($pageFn)
    {
        ++$this->pageNumber;
        $params = ['page' => $this->pageNumber, 'page_size' => $this->pageSize];
        $response = $pageFn($params);

        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        return $this->pageNumber == 0 || $position >= $this->pageNumber * $this->pageSize;
    }
}
