<?php

namespace Zendesk\API\Traits\Utility\Pagination;

class CbpStrategy extends PaginationStrategy
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
        $response = $this->resourcesRoot->findAll($params);

        // TODO: remove
        // print_r( $response);
        // echo "\npage ids: ";
        // foreach ($response->tickets as $ticket) {
        //     echo $ticket->id . " ";
        // }

        $this->afterCursor = $response->meta->has_more ? $response->meta->after_cursor : null;
        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        // TODO: calc position
        return !$this->started || $this->afterCursor;
    }
}
