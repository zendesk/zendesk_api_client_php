<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Http;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The AuditLogs class is as per http://developer.zendesk.com/documentation/rest_api/audit_logs.html
 */
class AuditLogs extends ResourceAbstract
{
    const OBJ_NAME = 'audit_log';
    const OBJ_NAME_PLURAL = 'audit_logs';

    use FindAll {
        findAll as traitFindAll;
    }

    use Find;

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
        $sideloads = $this->client->getSideload($params);

        $extraParams = Http::prepareQueryParams($sideloads, $params);
        $queryParams = array_filter(array_flip($params), [$this, 'filterParams']);
        $queryParams = array_flip($queryParams);

        $queryParams = array_merge($queryParams, $extraParams);

        return $this->traitFindAll($queryParams);
    }

    /**
     * Filter parameters passed and only allow valid query parameters.
     */
    private function filterParams($param)
    {
        return preg_match("/^filter[\"[a-zA-Z_]*\"]/", $param);
    }
}
