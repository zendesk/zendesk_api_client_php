<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\CreateMany;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\DeleteMany;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * Class OrganizationMemberships
 * https://developer.zendesk.com/rest_api/docs/core/organization_memberships
 */
class OrganizationMemberships extends ResourceAbstract
{
    use InstantiatorTrait;

    use Defaults;

    use CreateMany;
    use DeleteMany;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();
        $this->setRoute('makeDefault', 'users/{userId}/organization_memberships/{id}/make_default.json');
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
     * GET /api/v2/groups.json
     * GET /api/v2/users/{user_id}/groups.json
     *
     * @throws \Exception
     */
    public function getRoute($name, array $params = [])
    {
        $lastChained = $this->getLatestChainedParameter([self::class]);

        if ((empty($lastChained)) || ! (in_array($name, ['findAll', 'find', 'create', 'delete']))) {
            return parent::getRoute($name, $params);
        }

        $chainedResourceId    = reset($lastChained);
        $chainedResourceNames = array_keys($lastChained);
        $chainedResourceName  = (new $chainedResourceNames[0]($this->client))->resourceName;

        if ($name === 'findAll') {
            if (in_array($chainedResourceName, ['users', 'organizations'])) {
                return "{$chainedResourceName}/{$chainedResourceId}/{$this->resourceName}.json";
            }

            return "{$this->resourceName}.json";
        } elseif (in_array($name, ['find', 'delete'])) {
            if ($chainedResourceName === 'users') {
                return "{$chainedResourceName}/{$chainedResourceId}/{$this->resourceName}/{$params['id']}.json";
            }

            return "{$this->resourceName}/{$params['id']}.json";
        } elseif ($name === 'create') {
            if ($chainedResourceName === 'users') {
                return "{$chainedResourceName}/{$chainedResourceId}/{$this->resourceName}.json";
            }

            return "{$this->resourceName}.json";
        }
    }

    /**
     * Sets the default organization membership of a given user.
     *
     * @param array $params
     *
     * @return \stdClass | null
     * @throws MissingParametersException
     */
    public function makeDefault($params = [])
    {
        $params = $this->addChainedParametersToParams($params, ['id' => self::class, 'userId' => Users::class]);

        if (! $this->hasKeys($params, ['id', 'userId'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'userId']);
        }

        return $this->client->put($this->getRoute(__FUNCTION__, $params));
    }
}
