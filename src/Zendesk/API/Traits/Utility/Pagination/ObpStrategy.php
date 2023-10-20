<?php

namespace Zendesk\API\Traits\Utility\Pagination;

class ObpStrategy extends PaginationStrategy
{
    private $pageNumber = 0;

    public function getPage()
    {
        ++$this->pageNumber;
        $params = ['page' => $this->pageNumber, 'page_size' => $this->pageSize];
        $response = $this->resourcesRoot->findAll($params);
        return $response->{$this->resourcesKey};
    }

    public function isEndOfPage() {
        return true; // TODO: explain
    }
}
