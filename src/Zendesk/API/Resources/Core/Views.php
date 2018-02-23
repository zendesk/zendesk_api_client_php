<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Views class exposes view management methods
 */
class Views extends ResourceAbstract
{
    use Defaults {
        findAll as traitFindAll;
    }
    use \Zendesk\API\Traits\Resource\Search;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'findAllActive'  => 'views/active.json',
            'findAllCompact' => 'views/compact.json',
            'export'         => 'views/{id}/export.json',
            'preview'        => 'views/preview.json',
            'previewCount'   => 'views/preview/count.json',
            'execute'        => 'views/{id}/execute.json',
            'tickets'        => 'views/{id}/tickets.json',
        ]);
    }

    /**
     * List all active views
     *
     * @param array $params
     *
     * @return \stdClass | null
     */
    public function findAllActive(array $params = [])
    {
        return $this->traitFindAll($params, __FUNCTION__);
    }

    /**
     * List all active views
     *
     * @param array $params
     *
     * @return \stdClass | null
     */
    public function findAllCompact(array $params = [])
    {
        return $this->traitFindAll($params, __FUNCTION__);
    }

    /**
     * Execute a specific view
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function execute(array $params = [])
    {
        $id = $this->getIdFromParams($params);

        if (is_null($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        return $this->client->get($this->getRoute(__FUNCTION__, ['id' => $id]), $params);
    }

    /**
     * Get tickets from a specific view
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function tickets(array $params = [])
    {
        $id = $this->getIdFromParams($params);

        if (is_null($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        return $this->client->get($this->getRoute(__FUNCTION__, ['id' => $id]), $params);
    }

    /**
     * Count tickets (estimate) from a specific view or list of views
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function count(array $params = [])
    {
        $params = $this->addChainedParametersToParams(
            $params,
            ['id' => get_class($this)]
        );
        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $queryParams = $routeParams = [];
        if (is_array($params['id'])) {
            $this->setRoute(__FUNCTION__, 'views/count_many.json');
            $queryParams['ids'] = implode(',', $params['id']);
            unset($params['id']);
        } else {
            $this->setRoute(__FUNCTION__, 'views/{id}/count.json');
            $routeParams = ['id' => $params['id']];
        }

        return $this->client->get($this->getRoute(__FUNCTION__, $routeParams), array_merge($params, $queryParams));
    }

    /**
     * Export a view
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function export(array $params = [])
    {
        $id = $this->getIdFromParams($params);

        if (is_null($id)) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        return $this->client->get($this->getRoute(__FUNCTION__, ['id' => $id]), $params);
    }

    /**
     * Preview a view
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function preview(array $params)
    {
        return $this->client->post($this->getRoute(__FUNCTION__), [$this->objectName => $params]);
    }

    /**
     * Ticket count for a view preview
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function previewCount(array $params)
    {
        return $this->client->post($this->getRoute(__FUNCTION__), [$this->objectName => $params]);
    }

    /**
     * Get the ticket ID from the chained parameters or a params array
     *
     * @param array &$params
     * @return string
     */
    private function getIdFromParams(array &$params)
    {
        if (! isset($params['id'])) {
            $id = $this->getChainedParameter(get_class($this));
        } else {
            $id = $params['id'];
            unset($params['id']);
        }

        return $id;
    }
}
