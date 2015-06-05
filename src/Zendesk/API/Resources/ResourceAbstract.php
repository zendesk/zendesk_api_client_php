<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Http;

/**
 * Abstract class for all endpoints
 * @package Zendesk\API
 */
abstract class ResourceAbstract
{
    /**
     * @var String
     */
    protected $endpoint;

    /**
     * @var HttpClient
     */
    protected $client;
    /**
     * @var int
     */
    protected $lastId;

    /**
     * @var array
     */
    protected $chainedParameters = [];

    /**
     * @param HttpClient $client
     */
    public function __construct(\Zendesk\API\HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     *
     * Return the resource name using the name of the class (used for endpoints)
     *
     * @return string
     */
    private function getResourceNameFromClass()
    {
        $namespacedClassName = get_class($this);
        $resourceName = join('', array_slice(explode('\\', $namespacedClassName), -1));

        return strtolower($resourceName);
    }

    /**
     * Saves an id for future methods in the chain
     *
     * @param int $id
     *
     * @return $this
     */
    public function setLastId($id)
    {
        $this->lastId = $id;

        return $this;
    }

    /**
     * Sets the chained parameters
     *
     * @param $params
     *
     * @return $this
     *
     */
    public function setChainedParameters($params)
    {
        $this->chainedParameters = $params;

        return $this;
    }

    /**
     * Returns chained parameters
     *
     * @return $this
     */
    public function getChainedParameters()
    {

        return $this->chainedParameters;
    }

    /**
     * Saves an id for future methods in the chain
     *
     * @return int
     */
    public function getLastId()
    {
        return $this->lastId;
    }

    /**
     * Check that all parameters have been supplied
     *
     * @param array $params
     * @param array $mandatory
     *
     * @return bool
     */
    public function hasKeys(array $params, array $mandatory)
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (!array_key_exists($mandatory[$i], $params)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check that any parameter has been supplied
     *
     * @param array $params
     * @param array $mandatory
     *
     * @return bool
     */
    public function hasAnyKey(array $params, array $mandatory)
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (array_key_exists($mandatory[$i], $params)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enable side-loading (beta) - flags until the next chain
     *
     * @param array $fields
     *
     * @return $this
     */
    public function sideload(array $fields = array())
    {
        $this->client->setSideload($fields);

        return $this;
    }

    /**
     * List all of this resource
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array())
    {
        if (empty($this->endpoint)) {
            $this->endpoint = $this->getResourceNameFromClass() . '.json';
        }

        $sideloads = $this->client->getSideload($params);

        $queryParams = Http::prepareQueryParams($sideloads, $params);

        $response = Http::send_with_options(
            $this->client,
            $this->endpoint,
            ['queryParams' => $queryParams]
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Find a specific ticket by id or series of ids
     *
     * @param $id
     * @param array $queryQueryParams
     *
     * @return mixed
     * @throws MissingParametersException
     */
    public function find($id = null, array $queryParams = array())
    {
        if (empty($id)) {
            $chainedParameters = $this->getChainedParameters();
            $className = get_class($this);
            $id = isset($chainedParameters[$className]) ? $chainedParameters[$className] : null;
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }

        if (empty($this->endpoint)) {
            $this->endpoint = $this->getResourceNameFromClass() . "/{$id}.json";
        }

        $response = Http::send_with_options(
            $this->client,
            $this->endpoint,
            ["queryParams" => $queryParams]
        );
        $this->client->setSideload(null);

        return $response;
    }


    /**
     * Create a new view
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params)
    {
        // TODO: what happens when endpoint was already set by a different method call?

        if (empty($this->endpoint)) {
            $this->endpoint = $this->getResourceNameFromClass() . ".json";
        }

        $class = get_class($this);
        $response = Http::send_with_options(
            $this->client,
            $this->endpoint,
            [
                'postFields' => array($class::OBJ_NAME => $params),
                'method' => 'POST'
            ]
        );

        $this->client->setSideload(null);

        return $response;
    }


    /**
     * Update a resource
     *
     * @param array $updateResourceFields
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function update($id, array $updateResourceFields = [])
    {
        if (empty($this->endpoint)) {
            $this->endpoint = $this->getResourceNameFromClass() . "/$id.json";
        }

        $class = get_class($this);
        $postFields = array($class::OBJ_NAME => $updateResourceFields);

        $response = Http::send_with_options(
            $this->client,
            $this->endpoint,
            ['postFields' => $postFields, 'method' => 'PUT']
        );

        $this->client->setSideload(null);

        return $response;
    }


    /**
     * Delete a resource
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return bool
     */
    public function delete($id)
    {
        if (empty($this->endpoint)) {
            $this->endpoint = $this->getResourceNameFromClass() . "/$id.json";
        }

        $response = Http::send_with_options(
            $this->client,
            $this->endpoint,
            ['method' => 'DELETE']
        );

        $this->client->setSideload(null);

        return $response;
    }


}
