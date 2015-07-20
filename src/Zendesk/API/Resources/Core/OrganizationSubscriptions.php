<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;

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

        $chainedResourceNames = array_keys($lastChained);

        if (empty($lastChained) || $name !== 'findAll') {
            return parent::getRoute($name, $params);
        } else {
            $id       = reset($lastChained);
            $resource = $chainedResourceNames[0]::OBJ_NAME_PLURAL;

            if ('users' === $resource) {
                return "users/$id/organization_subscriptions.json";
            } elseif ('organizations' === $resource) {
                return "organizations/$id/subscriptions.json";
            } else {
                return 'organization_subscriptions.json';
            }
        }
    }
}
