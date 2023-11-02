<?php
namespace Zendesk\API\Traits\Utility\Pagination;

abstract class AbstractStrategy
{
    public const DEFAULT_PAGE_SIZE = 100;

    /**
     * @var string The response key where the data is returned
     */
    protected $resourcesKey;
    protected $pageSize;

    public function __construct($resourcesKey, $pageSize = self::DEFAULT_PAGE_SIZE)
    {
        $this->resourcesKey = $resourcesKey;
        $this->pageSize = $pageSize;
    }

    public function orderParams($params)
    {
        return $params;
    }

    abstract public function getPage($pageFn);
    abstract public function shouldGetPage($position);
}
