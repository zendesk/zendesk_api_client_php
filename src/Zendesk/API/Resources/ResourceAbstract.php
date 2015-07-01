<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Http;
use Zendesk\API\HttpClient;
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
    protected $resourceName;

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
    protected $routes = [];

    /**
     * @var array
     */
    protected $additionalRouteParams = [];

    /**
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        $this->client = $client;

        if (! isset($this->resourceName)) {
            $this->resourceName = $this->getResourceNameFromClass();
        }


        $this->setUpRoutes();
    }

    /**
     * This returns the valid relations of this resource. Definition of what is allowed to chain after this resource.
     * Make sure to add in this method when adding new sub resources.
     * Example:
     *    $client->ticket()->comments();
     *    Where ticket would have a comments as a valid sub resource.
     *    The array would look like:
     *      ['comments' => '\Zendesk\API\Resources\TicketComments']
     * @return array
     */
    public static function getValidSubResource()
    {
        return [];
    }

    /**
     * Return the resource name using the name of the class (used for endpoints)
     * @return string
     */
    private function getResourceNameFromClass()
    {
        $namespacedClassName = get_class($this);
        $resourceName        = join('', array_slice(explode('\\', $namespacedClassName), -1));

        // This converts the resource name from camel case to underscore case.
        // e.g. MyClass => my_class
        $underscored = strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', $resourceName));

        return strtolower($underscored);
    }

    /**
     * @return String
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Sets up the available routes for the resource.
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'findAll' => "{$this->resourceName}.json",
            'find'    => "{$this->resourceName}/{id}.json",
            'create'  => "{$this->resourceName}.json",
            'update'  => "{$this->resourceName}/{id}.json",
            'delete'  => "{$this->resourceName}/{id}.json"
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
            if (! array_key_exists($mandatory[$i], $params)) {
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
    public function sideload(array $fields = [])
    {
        $this->client->setSideload($fields);

        return $this;
    }

    /**
     * Wrapper for adding multiple routes via setRoute
     *
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
     *
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
     *
     * @param       $name
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function getRoute($name, array $params = [])
    {
        if (! isset($this->routes[$name])) {
            throw new \Exception('Route not found.');
        }

        $route = $this->routes[$name];

        $substitutions = array_merge($params, $this->getAdditionalRouteParams());
        foreach ($substitutions as $name => $value) {
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
     * @throws \Exception
     * @return mixed
     */
    public function findAll(array $params = [])
    {
        $sideloads = $this->client->getSideload($params);

        $queryParams = Http::prepareQueryParams($sideloads, $params);

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, $params),
            ['queryParams' => $queryParams]
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Find a specific ticket by id or series of ids
     *
     * @param       $id
     * @param array $queryParams
     *
     * @return mixed
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

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $id]),
            ['queryParams' => $queryParams]
        );
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * @param array $additionalRouteParams
     */
    public function setAdditionalRouteParams($additionalRouteParams)
    {
        $this->additionalRouteParams = $additionalRouteParams;
    }

    /**
     * @return array
     */
    public function getAdditionalRouteParams()
    {
        return $this->additionalRouteParams;
    }

    /**
     * Create a new resource
     *
     * @param array $params
     *
     * @throws \Exception
     * @return mixed
     */
    public function create(array $params)
    {
        $class    = get_class($this);
        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute('create'),
            [
                'postFields' => [$class::OBJ_NAME => $params],
                'method'     => 'POST'
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
     * @throws \Exception
     * @return mixed
     */
    public function update($id = null, array $updateResourceFields = [])
    {
        $class = get_class($this);
        if (empty($id)) {
            $id = $this->getChainedParameter($class);
        }

        $postFields = [$class::OBJ_NAME => $updateResourceFields];

        $response = Http::sendWithOptions(
            $this->client,
            $this->getRoute(__FUNCTION__, ['id' => $id]),
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
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        $route    = $this->getRoute('find', ['id' => $id]);
        $response = Http::sendWithOptions(
            $this->client,
            $route,
            ['method' => 'DELETE']
        );

        $this->client->setSideload(null);

        return $response;
    }
}
