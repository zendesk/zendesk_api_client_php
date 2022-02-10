<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The Tags class exposes methods as detailed on https://developer.zendesk.com/api-reference/event-connectors/webhooks/webhooks/
 */
class Webhooks extends ResourceAbstract
{
    use Defaults;
    use FindAll {
        findAll as traitFindAll;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'create'       => "{$this->resourceName}",
            'update'       => "{$this->resourceName}/{id}",
            'delete'       => "{$this->resourceName}",
            'findAll'      => "{$this->resourceName}",
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(array $params = [])
    {
        $queryParams = array_filter(array_flip($params), [$this, 'filterParams']);
        $queryParams = array_merge($params, array_flip($queryParams));

        return $this->traitFindAll($queryParams);
    }

    /**
     * Filter parameters passed and only allow valid query parameters.
     *
     * @param $param
     * @return int
     */
    private function filterParams($param)
    {
        return preg_match("/^sort|page[[a-zA-Z_]*]|filter[[a-zA-Z_]*](\\[\\]?)/", $param);
    }
}
