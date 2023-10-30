<?php

namespace Zendesk\API\Traits\Utility\Pagination;

class CbpStrategy extends AbstractStrategy
{
    private $afterCursor = null;
    private $started = false;

    public function getPage()
    {
        $this->started = true;
        $params = ['page[size]' => $this->pageSize];
        if ($this->afterCursor) {
            $params['page[after]'] = $this->afterCursor;
        }
        $response = $this->clientList->findAll($params);

        $this->afterCursor = $response->meta->has_more ? $response->meta->after_cursor : null;
        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        return !$this->started || $this->afterCursor;
    }
}
