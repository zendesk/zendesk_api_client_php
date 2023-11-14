<?php

namespace Zendesk\API\Traits\Utility\Pagination;

/**
 * Cursor Based Pagination
 * Used in paginationStrategyClass
 */
class CbpStrategy extends AbstractStrategy
{
    private $afterCursor;
    private $hasMore;
    private $started = false;

    public function page($getPageFn)
    {
        $this->started = true;
        $this->latestResponse = $getPageFn();
        $this->hasMore = $this->latestResponse->meta->has_more;
        if (isset($this->latestResponse->meta->after_cursor)) {
            $this->afterCursor = $this->latestResponse->meta->after_cursor;
        }

        return $this->latestResponse->{$this->resourcesKey};
    }

    public function shouldGetPage($position) {
        return !$this->started || $this->hasMore;
    }

    public function params()
    {
        $result = array_merge($this->params, $this->paginationParams(), $this->sortParams());
        $result = $this->unsetObpParams($result);

        return $result;
    }

    /**
     * The params that are needed to ordering in CBP (eg: ["sort" => "-age"])
     * If OBP params are passed, they are converted to CBP
     *
     * OBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort_order=desc&sort_by=updated_at&per_page=2
     * CBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort=-updated_at&page[size]=2
     *
     * @return array Params with proper CBP sorting order
     */
    private function sortParams()
    {
        if (isset($this->params['sort_by']) && !isset($this->params['sort'])) {
            $direction = (isset($this->params['sort_order']) && strtolower($this->params['sort_order']) === 'desc') ? '-' : '';
            return array_merge($this->params, ['sort' => $direction . $this->params['sort_by']]);
        } else {
            return [];
        }
    }
    /**
     * The params that are needed to for pagination (eg: ["page[size]" => "100"])
     * If OBP params are passed, they are converted to CBP
     *
     * @return array Params for pagination
     */
    private function paginationParams()
    {
        $result = isset($this->afterCursor) ? ['page[after]' => $this->afterCursor] : [];

        return array_merge(['page[size]' => $this->pageSize()], $result);
    }

    private function unsetObpParams($params)
    {
        unset(
            $params['page'],
            $params['per_page'],
            $params['sort_by'],
            $params['sort_order']
        );
        return $params;
    }
}
