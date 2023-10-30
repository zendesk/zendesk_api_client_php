<?php
namespace Zendesk\API\Traits\Utility\Pagination;

abstract class AbstractStrategy
{
    // TODO: 100
    public const DEFAULT_PAGE_SIZE = 2;

    /*
     * The object handling the list, Ie: `$client->{clientResources}()`
     */
    protected $clientResources;

    /*
     * The response key where the data is returned
     */
    protected $resourcesKey;
    protected $pageSize;

    public function __construct($clientResources, $resourcesKey, $pageSize = self::DEFAULT_PAGE_SIZE)
    {
        $this->clientResources = $clientResources;
        $this->resourcesKey = $resourcesKey;
        $this->pageSize = $pageSize;
    }

    abstract public function getPage();
    abstract public function shouldGetPage($position);
}
