<?php

namespace Zendesk\API\Traits\Utility\Pagination;


/**
 * Offset Based Pagination
 */
class ObpStrategy extends AbstractStrategy
{
    private $pageNumber = 0;

    public function getPage()
    {
        ++$this->pageNumber;
        $params = ['page' => $this->pageNumber, 'page_size' => $this->pageSize];
        $response = $this->resourcesRoot->findAll($params);

        // TODO: remove
        // echo "\npage ids: ";
        // foreach ($response->{$this->resourcesRoot} as $item) {
        //     echo $item->id . " ";
        // }

        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        return $this->pageNumber == 0 || $position >= $this->pageNumber * $this->pageSize;
    }
}
