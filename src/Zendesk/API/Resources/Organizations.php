<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Http;
use Zendesk\API\Traits\Resource\CreateMany;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\DeleteMany;
use Zendesk\API\Traits\Resource\FindMany;
use Zendesk\API\Traits\Resource\UpdateMany;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * Class Organizations
 */
class Organizations extends ResourceAbstract
{
    const OBJ_NAME = 'organization';
    const OBJ_NAME_PLURAL = 'organizations';

    use InstantiatorTrait;
    use Defaults;
    use FindMany;
    use CreateMany;
    use UpdateMany;
    use DeleteMany;

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources()
    {
        return [
            'organizationMemberships' => OrganizationMemberships::class,
            'subscriptions'           => OrganizationSubscriptions::class,
        ];
    }

    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes(
            [
                'autocomplete' => $this->resourceName . '/autocomplete.json',
                'related'      => $this->resourceName . '/{id}/related.json',
                'search'       => $this->resourceName . '/search.json',
            ]
        );
    }

    /**
     * Returns a route and replaces tokenized parts of the string with
     * the passed params
     *
     * @param       $name
     * @param array $params
     *
     * @return mixed The default routes, or if $name is set to `findAll`, any of the following formats
     * based on the parent chain
     * GET /api/v2/organizations.json
     * GET /api/v2/users/{user_id}/organizations.json
     *
     * @throws \Exception
     */
    public function getRoute($name, array $params = [])
    {
        $lastChained = $this->getLatestChainedParameter();

        $chainedResourceNames = array_keys($lastChained);

        if (empty($lastChained) || $name !== 'findAll') {
            return parent::getRoute($name, $params);
        } else {
            $id       = reset($lastChained);
            $resource = $chainedResourceNames[0]::OBJ_NAME_PLURAL;

            if ('users' === $resource) {
                return "users/$id/organizations.json";
            } else {
                return 'organizations.json';
            }
        }
    }

    /**
     * Returns an array of organizations whose name starts with the value specified in the name parameter.
     * The name must be at least 2 characters in length
     *
     * @param       $name
     * @param array $params
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function autocomplete($name, array $params = [])
    {
        $sideloads = $this->client->getSideload($params);

        $queryParams         = Http::prepareQueryParams($sideloads, $params);
        $queryParams['name'] = $name;

        return $this->client->get($this->getRoute(__FUNCTION__), $queryParams);
    }

    /**
     * Show an organization's related information
     *
     * @param $id Organization ID
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function related($id)
    {
        return $this->client->get($this->getRoute(__FUNCTION__, ['id' => $id]));
    }

    /**
     * Seach organizations by external ID
     *
     * @param       $external_id
     * @param array $params
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function search($external_id, array $params = [])
    {
        $sideloads = $this->client->getSideload($params);

        $queryParams                = Http::prepareQueryParams($sideloads, $params);
        $queryParams['external_id'] = $external_id;

        return $this->client->get($this->getRoute(__FUNCTION__), $queryParams);
    }
}
