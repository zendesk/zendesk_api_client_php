<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

/**
 * Class OrganizationSubscriptions
 */
class OrganizationSubscriptions extends ResourceAbstract
{
    use Defaults;

    /**
     * Returns a route and replaces tokenized parts of the string with
     * the passed params
     *
     * @param       $name
     * @param array $params
     *
     * @return mixed The default routes, or if $name is set to `findAll`, any of the following formats
     * based on the parent chain
     * GET /api/v2/organizations/{organization_id}/subscriptions.json
     * GET /api/v2/organization_subscriptions.json
     * GET /api/v2/users/{user_id}/organization_subscriptions.json
     *
     * @throws \Exception
     */
    public function getRoute($name, array $params = [])
    {
        $lastChained = $this->getLatestChainedParameter();

        if (empty($lastChained) || $name !== 'findAll') {
            return parent::getRoute($name, $params);
        } else {
            $id                   = reset($lastChained);
            $chainedResourceNames = array_keys($lastChained);
            $chainedResourceName  = (new $chainedResourceNames[0]($this->client))->resourceName;

            if ('users' === $chainedResourceName) {
                return "users/$id/organization_subscriptions.json";
            } elseif ('organizations' === $chainedResourceName) {
                return "organizations/$id/subscriptions.json";
            } else {
                return 'organization_subscriptions.json';
            }
        }
    }
}
