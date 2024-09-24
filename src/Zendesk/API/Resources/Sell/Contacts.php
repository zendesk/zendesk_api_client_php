<?php

namespace Zendesk\API\Resources\Sell;

use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * Contacts class provides a simple interface to manage your contacts in Sell
 * https://developers.getbase.com/docs/rest/reference/contacts
 */
class Contacts extends ResourceAbstract
{
    use InstantiatorTrait;
    use Defaults;

    // Override constructor to set different API base path
    public function __construct($client)
    {
        parent::__construct($client, 'v2/');
    }

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'data';

    /**
     * {@inheritdoc}
     */
    protected $resourceName = 'contacts';

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            "find"    => "{$this->resourceName}/{id}",
            "findAll" => $this->resourceName,
            "create"  => $this->resourceName,
            "update"  => "{$this->resourceName}/{id}",
            "delete"  => "{$this->resourceName}/{id}",
            "upsert"  => "{$this->resourceName}/upsert",
        ]);
    }

    /**
     * Create a new contact or update an existing, based on a value of a filter or a set of filters
     * @param array $params
     * @param array $updateResourceFields
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function upsert(array $params, array $updateResourceFields)
    {
        $route = $this->getRoute(__FUNCTION__);

        return $this->client->post(
            $route,
            [$this->objectName => $updateResourceFields],
            ['queryParams' => $params]
        );
    }
}
