<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The AuditLogs class is as per http://developer.zendesk.com/documentation/rest_api/audit_logs.html
 */
class AuditLogs extends ResourceAbstract
{
    use Find;
    use FindAll {
        findAll as traitFindAll;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'findAll' => "{$this->resourceName}.json",
            'find'    => "{$this->resourceName}/{id}.json",
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
        return preg_match("/^sort_by|sort_order|filter[[a-zA-Z_]*](\\[\\]?)/", $param);
    }
}
