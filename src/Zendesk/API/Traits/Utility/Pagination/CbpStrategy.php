<?php

namespace Zendesk\API\Traits\Utility\Pagination;

/**
 * Cursor Based Pagination
 * Used in paginationStrategyClass
 */
class CbpStrategy extends AbstractStrategy
{
    private $afterCursor = null;
    private $started = false;

    public function getPage($pageFn)
    {
        $this->started = true;

        $response = $pageFn($this->paginationParams());

        $this->afterCursor = $response->meta->has_more ? $response->meta->after_cursor : null;
        return $response->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        return !$this->started || $this->afterCursor;
    }

    public function orderParams($params)
    {
        if (isset($params['sort']) || !isset($params['sort_by'])) {
            return $params;
        } else {
            $direction = (isset($params['sort_order']) && strtolower($params['sort_order']) === 'desc') ? '-' : '';
            $result = array_merge($params, ['sort' => $direction . $params['sort_by']]);
            unset($result['sort_by'], $result['sort_order']);
            return $result;
        }
    }
    private function paginationParams()
    {
        // TODO: per_page...
        $result = ['page[size]' => $this->pageSize];
        if ($this->afterCursor) {
            $result['page[after]'] = $this->afterCursor;
        }

        return $result;
    }
}
