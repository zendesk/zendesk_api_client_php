<?php

namespace Zendesk\API\Resources;

use Zendesk\API\UtilityTraits\InstantiatorTrait;

class Groups extends ResourceAbstract
{
    const OBJ_NAME = 'group';
    const OBJ_NAME_PLURAL = 'groups';

    use InstantiatorTrait;

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
            $resource = $chainedResourceNames[0]::OBJ_NAME_PLURAL;

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
     * @return mixed
     */
    public function assignable()
    {
        return $this->client->get($this->getRoute(__FUNCTION__));
    }
}
