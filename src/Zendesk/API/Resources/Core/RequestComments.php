<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The Requests class exposes request management methods
 * Note: you must authenticate as a user!
 */
class RequestComments extends ResourceAbstract
{
    use FindAll {
        findAll as traitFindAll;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'find'    => 'requests/{requestId}/comments/{id}.json',
            'findAll' => 'requests/{requestId}/comments.json',
        ]);
    }

    public function findAll(array $params = [])
    {
        $params = $this->addChainedParametersToParams($params, ['requestId' => Requests::class]);

        return $this->traitFindAll($params);
    }

    /**
     * Find a specific ticket by id or series of ids
     *
     * @param       $id
     * @param array $queryParams
     *
     * @return \stdClass | null
     * @throws MissingParametersException
     * @throws \Exception
     */
    public function find($id = null, array $queryParams = [])
    {
        if (empty($id)) {
            $id = $this->getChainedParameter(get_class($this));
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        if (! ($requestId = $this->getChainedParameter(Requests::class))) {
            throw new MissingParametersException(__METHOD__, ['requestId']);
        }

        $route = $this->getRoute(__FUNCTION__, ['id' => $id, 'requestId' => $requestId]);

        return $this->client->get(
            $route,
            $queryParams
        );
    }
}
