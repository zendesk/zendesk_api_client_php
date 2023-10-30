<?php
namespace Zendesk\API\Traits\Utility\Pagination;

abstract class AbstractStrategy
{
    public const DEFAULT_PAGE_SIZE = 100;

    /*
     * @var mixed use trait FindAll. The object handling the list, Ie: `$client->{clientList}()`
     */
    protected $clientList;

    /*
     * @var string The response key where the data is returned
     */
    protected $resourcesKey;
    protected $pageSize;

    public function __construct($clientList, $resourcesKey, $pageSize = self::DEFAULT_PAGE_SIZE)
    {
        $this->clientList = $clientList;
        $this->resourcesKey = $resourcesKey;
        $this->pageSize = $pageSize;
    }

    abstract public function getPage();
    abstract public function shouldGetPage($position);
}
