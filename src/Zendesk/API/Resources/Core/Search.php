<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;

/**
 * Class Search
 */
class Search extends ResourceAbstract
{
    /**
     * {@inheritdoc}
     */
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
     * @return \stdClass | null
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

    /**
     * This resource behaves the same as /api/v2/search, but lets anonymous users search public forums in the Web
     * portal. The endpoint searches only articles, not tickets, and returns only articles that
     * the requesting user is allowed to see.
     *
     * @param       $query
     * @param array $queryParams
     *
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function anonymous($query, $queryParams = [])
    {
        $queryParams['query'] = $query;

        return $this->client->get($this->getRoute(__FUNCTION__), $queryParams);
    }
}
