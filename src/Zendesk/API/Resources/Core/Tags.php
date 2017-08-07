<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * The Tags class exposes methods as detailed on http://developer.zendesk.com/documentation/rest_api/tags.html
 *
 * @package Zendesk\API
 */
class Tags extends ResourceAbstract
{
    use Defaults;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'tags';
    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'tags';

    /**
     * Returns a route and replaces tokenized parts of the string with
     * the passed params
     *
     * @param       $name
     * @param array $params
     *
     * @return mixed Any of the following formats based on the parent chain
     *              tickets/{id}/tags.json
     *              topics/{id}/tags.json
     *              organizations/{id}/tags.json
     *              users/{id}/tags.json
     *
     * @throws \Exception
     */
    public function getRoute($name, array $params = [])
    {
        $allowedRoutes = ['update', 'find', 'create', 'delete'];

        if (! in_array($name, $allowedRoutes)) {
            return parent::getRoute($name, $params);
        }

        $lastChained = $this->getLatestChainedParameter();

        if (empty($lastChained)) {
            throw new CustomException('The ' . $name . '() method needs to be called while chaining.');
        }

        $id                   = reset($lastChained);
        $chainedResourceNames = array_keys($lastChained);
        $resource             = (new $chainedResourceNames[0]($this->client))->resourceName;

        return "$resource/$id/tags.json";
    }
}
