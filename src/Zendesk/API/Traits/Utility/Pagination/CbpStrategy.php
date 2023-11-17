<?php

namespace Zendesk\API\Traits\Utility\Pagination;

/**
 * Cursor Based Pagination
 * Used in PaginationIterator
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
        if (!isset($this->latestResponse->meta->has_more)) {
            throw new PaginationError(
                "Response not conforming to the CBP format, if you think your request is correct, please open an issue at https://github.com/zendesk/zendesk_api_client_php/issues"
            );
        }
        $this->hasMore = $this->latestResponse->meta->has_more;
        if (isset($this->latestResponse->meta->after_cursor)) {
            $this->afterCursor = $this->latestResponse->meta->after_cursor;
        }

        return $this->latestResponse->{$this->resourcesKey};
    }

    public function shouldGetPage($current_page) {
        return !$this->started || $this->hasMore;
    }

    public function params()
    {
        $result = array_merge($this->params, $this->paginationParams());
        $result = $this->unsetObpParams($result);

        return $result;
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
