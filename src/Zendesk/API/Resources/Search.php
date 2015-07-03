<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;

class Search extends ResourceAbstract
{
    protected function setUpRoutes()
    {
        $this->setRoutes(
            [
                'find'      => 'search.json',
                'anonymous' => 'portal/search.json'
            ]
        );
    }

    /**
     *
     * The search API is a unified search API that returns tickets, users, and organizations. You can define filters to
     * narrow your search results according to resource type, dates, and object properties, such as ticket requester or
     * tag.
     *
     * @param null  $query
     * @param array $queryParams
     *
     * @throws MissingParametersException
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function find($query = null, array $queryParams = [])
    {
        if (empty($query)) {
            throw new MissingParametersException(__METHOD__, ['query']);
        }

        $queryParams['query'] = $query;

        return $this->client->get($this->getRoute(__FUNCTION__), $queryParams);
    }

    public function anonymous($query, $queryParams = [])
    {
        $queryParams['query'] = $query;

        return $this->client->get($this->getRoute(__FUNCTION__), $queryParams);
    }
}
