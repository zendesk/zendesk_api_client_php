<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;

/**
 * The Views class exposes view management methods
 *
 * @package Zendesk\API
 */
class Views extends ResourceAbstract
{

    const OBJ_NAME = 'view';
    const OBJ_NAME_PLURAL = 'views';

    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'findAll'      => 'views{modifier}.json',
            'export'       => 'views/{id}/export.json',
            'preview'      => 'views/preview.json',
            'previewCount' => 'views/preview/count.json',
            'execute'      => 'views/{id}/execute.json',
            'tickets'      => 'views/{id}/tickets.json',
        ]);
    }

    /**
     * List all views
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = [])
    {
        if (isset($params['active'])) {
            $params['modifier'] = '/active';
        } elseif (isset($params['compact'])) {
            $params['modifier'] = '/compact';
        } else {
            $params['modifier'] = '';
        }

        return parent::findAll($params);
    }

    /**
     * Show a specific view
     *
     * @param int   $id
     * @param array $queryParams
     *
     * @return mixed
     */
    public function find($id = null, array $queryParams = [])
    {
        $queryParams = Http::prepareQueryParams(
            $this->client->getSideload($queryParams),
            $queryParams
        );

        return parent::find($id, $queryParams);
    }

    /**
     * Execute a specific view
     *
     * @param null $id
     *
     * @return mixed
     * @throws MissingParametersException
     *
     */
    public function delete($id = null)
    {
        if ((empty($id)) && ! ($this->getChainedParameter('id', false))) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $endPoint = 'views/' . $id . '.json';

        $response = Http::send(
            $this->client,
            $endPoint,
            ['method' => 'DELETE']
        );

        return $response;
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
     * @return mixed
     */
    public function execute(array $params = [])
    {
        $params = $this->addChainedParametersToParams(
            $params,
            ['id' => get_class($this)]
        );

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $queryParams = Http::prepareQueryParams(
            $this->client->getSideload($params),
            $params
        );

        return $this->client->get($this->getRoute(__FUNCTION__, ['id' => $params['id']]), $queryParams);
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
     * @return mixed
     */
    public function tickets(array $params = [])
    {
        $params = $this->addChainedParametersToParams(
            $params,
            ['id' => get_class($this)]
        );

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $queryParams = Http::prepareQueryParams(
            $this->client->getSideload($params),
            $params
        );

        return $this->client->get($this->getRoute(__FUNCTION__, ['id' => $params['id']]), $queryParams);
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
     * @return mixed
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

        $extraParams = Http::prepareQueryParams(
            $this->client->getSideload($params),
            $params
        );

        return $this->client->get($this->getRoute(__FUNCTION__, $routeParams), $queryParams);
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
     * @return mixed
     */
    public function export(array $params = [])
    {
        $params = $this->addChainedParametersToParams(
            $params,
            ['id' => get_class($this)]
        );
        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $queryParams = Http::prepareQueryParams(
            $this->client->getSideload($params),
            $params
        );

        return $this->client->get($this->getRoute(__FUNCTION__, ['id' => $params['id']]), $queryParams);
    }

    /**
     * Preview a view
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function preview(array $params)
    {
        $extraParams = Http::prepareQueryParams(
            $this->client->getSideload($params),
            $params
        );

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            [
                'postFields'  => ['view' => $params],
                'queryParams' => $extraParams,
                'method'      => 'POST'
            ]
        );

        return $response;
    }

    /**
     * Ticket count for a view preview
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function previewCount(array $params)
    {
        $extraParams = Http::prepareQueryParams(
            $this->client->getSideload($params),
            $params
        );

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            [
                'postFields'  => ['view' => $params],
                'queryParams' => $extraParams,
                'method'      => 'POST'
            ]
        );

        return $response;
    }
}
