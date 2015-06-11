<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;
use Zendesk\API\UtilityTraits\ChainedParametersTrait;

/**
 * Abstract class for all endpoints
 * @package Zendesk\API
 */
abstract class ResourceAbstract
{
    use ChainedParametersTrait;

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
    protected $routes;

    /**
     * @param \Zendesk\API\HttpClient $client
     */
    public function __construct(\Zendesk\API\HttpClient $client)
    {
        $this->client = $client;

        $this->setUpRoutes();
    }

    /**
     * This returns the valid relations of this resource. Definition of what is allowed to chain after this resource.
     * Example:
     *    $client->ticket()->comments();
     *    Where ticket would have a comments as a valid sub resource.
     *    The array would look like:
     *      ['comments' => '\Zendesk\API\Resources\TicketComments']
     *
     * @return array
     */
    public static function getValidSubResource() {
        return [];
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

    protected function setUpRoutes()
    {
        if (isset($this->endpoint)) {
            $resource = $this->endpoint;
        } else {
            $resource = $this->getResourceNameFromClass();
        }

        $this->setRoutes([
            'findAll' => "$resource.json",
            'find'    => "$resource/{id}.json",
            'create'  => "$resource.json",
            'update'  => "$resource/{id}.json",
            'delete'  => "$resource/{id}.json"
        ]);
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
     * Wrapper for adding multiple routes via setRoute
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        foreach ($routes as $name => $route) {
            $this->setRoute($name, $route);
        }
    }

    /**
     * Add or override an existing route
     * @param $name
     * @param $route
     */
    public function setRoute($name, $route)
    {
        $this->routes[$name] = $route;
    }

    /**
     * Return all routes for this resource
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Returns a route and replaces tokenized parts of the string with
     * the passed params
     * @param       $name
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function getRoute($name, array $params = array())
    {
        if (!isset($this->routes[$name])) {
            throw new \Exception('Route not found.');
        }

        $route = $this->routes[$name];
        foreach ($params as $name => $value) {
            if (is_scalar($value)) {
                $route = str_replace('{' . $name . '}', $value, $route);
            }
        }

        return $route;
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
        $sideloads = $this->client->getSideload($params);

        $queryParams = Http::prepareQueryParams($sideloads, $params);

        $response = Http::send_with_options(
            $this->client,
            $this->getRoute('findAll', $params),
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
     *
     * @return mixed
     * @throws MissingParametersException
     * @throws \Exception
     *
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

        $response = Http::send_with_options(
            $this->client,
            $this->getRoute(__FUNCTION__, array('id' => $id)),
            ['queryParams' => $queryParams]
        );
        $this->client->setSideload(null);

        return $response;
    }


    /**
     * Create a new resource
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params)
    {
        $class = get_class($this);
        $response = Http::send_with_options(
            $this->client,
            $this->getRoute('create'),
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
    public function update($id = null, array $updateResourceFields = [])
    {
        $class = get_class($this);
        if (empty($id))
            $id = $this->getChainedParameter($class);

        $postFields = array($class::OBJ_NAME => $updateResourceFields);

        $response = Http::send_with_options(
            $this->client,
            $this->getRoute(__FUNCTION__, array('id' => $id)),
            ['postFields' => $postFields, 'method' => 'PUT']
        );

        $this->client->setSideload(null);

        return $response;
    }


    /**
     * Delete a resource
     *
     * @param null $id
     *
     * @return bool
     * @throws MissingParametersException
     * @throws \Exception
     *
     */
    public function delete($id = null)
    {
        if (empty($id)) {
            $chainedParameters = $this->getChainedParameters();
            if (array_key_exists(get_class($this), $chainedParameters)) {
                $id = $chainedParameters[get_class($this)];
            }
        }

        if (empty($id)) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }

        $route = $this->getRoute('find', array('id' => $id));
        $response = Http::send_with_options(
            $this->client,
            $route,
            ['method' => 'DELETE']
        );

        $this->client->setSideload(null);

        return $response;
    }


}
