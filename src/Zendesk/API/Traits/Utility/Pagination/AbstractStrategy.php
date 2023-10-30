<?php
namespace Zendesk\API\Traits\Utility\Pagination;

abstract class AbstractStrategy
{
    // TODO: 100
    public const DEFAULT_PAGE_SIZE = 2;

    protected $resourcesRoot;
    protected $resourcesKey;
    protected $pageSize;

    public function __construct($resourcesRoot, $resourcesKey, $pageSize = self::DEFAULT_PAGE_SIZE)
    {
        $this->resourcesRoot = $resourcesRoot;
        $this->resourcesKey = $resourcesKey;
        $this->pageSize = $pageSize;
    }

    abstract public function getPage();
    abstract public function shouldGetPage($position);
}
