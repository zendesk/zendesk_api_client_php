<?php

namespace Zendesk\API;

/**
 * Abstract class for all endpoints
 * @package Zendesk\API
 */
abstract class ResourceAbstract
{

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
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
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
     * @param array $queryParams
     * @return mixed
     *
     */
    public function find($id, array $queryParams = array())
    {
        // lastId is set when tickets is instantiated, and is either a ticket id or an array of ticket IDs
        // lastId doesn't have to be set, id can be passed in via $params
        if ($this->lastId != null) {
            $id = $this->lastId;
            $this->lastId = null;
        }

        if (empty($this->endpoint)) {
            $this->endpoint = $this->getResourceNameFromClass() . "/{$id}.json";
        }

        $response = Http::send($this->client, $this->endpoint, $queryParams);
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


}
