<?php
namespace Zendesk\API\Traits\Utility\Pagination;

abstract class AbstractStrategy
{
    /**
     * @var string The response key where the data is returned
     */
    protected $resourcesKey;
    protected $params;
    protected $pageSize;
    protected $latestResponse;

    public function __construct($resourcesKey, $params)
    {
        $this->resourcesKey = $resourcesKey;
        $this->params = $params;
    }

    public function params()
    {
        return $this->params;
    }

    /**
     * Returns the latest HTTP response, unless an error occurred, which causes an exception
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function latestResponse()
    {
        return $this->latestResponse;
    }

    /**
     * From the params or the default value
     *
     * @return integer
     */
    protected function pageSize()
    {
        if (isset($this->pageSize)) {
            return $this->pageSize;
        } else if (isset($this->params['page[size]'])) {
            $this->pageSize = $this->params['page[size]'];
        } else if (isset($this->params['per_page'])) {
            $this->pageSize = $this->params['per_page'];
        } else {
            $this->pageSize = DEFAULT_PAGE_SIZE;
        }

        return $this->pageSize;
    }

    abstract public function page($getPageFn);
    abstract public function shouldGetPage($current_page);
}
