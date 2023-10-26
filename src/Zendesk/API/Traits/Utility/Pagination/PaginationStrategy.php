<?php
// TODO: naming redundancies
namespace Zendesk\API\Traits\Utility\Pagination;

abstract class PaginationStrategy
{
    public const DEFAULT_PAGE_SIZE = 100;

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
