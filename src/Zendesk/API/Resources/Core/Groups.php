<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * Class Groups
 *
 * @method GroupMemberships memberships()
 */
class Groups extends ResourceAbstract
{
    use InstantiatorTrait;

    use Defaults;

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources()
    {
        return [
            'memberships' => GroupMemberships::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();
        $this->setRoute('assignable', 'groups/assignable.json');
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
        $lastChained = $this->getLatestChainedParameter();

        $chainedResourceNames = array_keys($lastChained);

        if (empty($lastChained) || $name !== 'findAll') {
            return parent::getRoute($name, $params);
        } else {
            $id       = reset($lastChained);
            $resource = (new $chainedResourceNames[0]($this->client))->resourceName;

            if ('users' === $resource) {
                return "users/$id/groups.json";
            } else {
                return 'groups.json';
            }
        }
    }

    /**
     * Show assignable groups
     *
     * @return \stdClass | null
     */
    public function assignable()
    {
        return $this->client->get($this->getRoute(__FUNCTION__));
    }
}
