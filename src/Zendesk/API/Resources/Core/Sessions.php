<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\RouteException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Delete;
use Zendesk\API\Traits\Resource\DeleteMany;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * Class Sessions
 * https://developer.zendesk.com/rest_api/docs/core/sessions
 */
class Sessions extends ResourceAbstract
{
    use Find;
    use FindAll;
    use Delete;

    use DeleteMany;

    protected $resourceName = 'sessions';

    protected function setUpRoutes()
    {
        $this->setRoutes([
            'current'            => 'users/me/sessions.json',
            'delete'             => 'users/{userId}/sessions/{id}.json',
            'deleteUserSessions' => 'users/{userId}/sessions.json',
            'find'               => 'users/{userId}/sessions/{id}.json',
            'logout'             => 'users/me/logout.json',
        ]);
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
     * /api/v2/sessions.json
     * /api/v2/users/{userId}/sessions.json
     *
     * @throws \Exception
     */
    public function getRoute($name, array $params = [])
    {
        $userId = $this->getChainedParameter(Users::class);

        if (in_array($name, ['delete', 'deleteUserSessions', 'find', 'findAll']) && ! is_null($userId)) {
            if ($name === 'findAll') {
                return "users/{$userId}/sessions.json";
            }

            $params = $this->addChainedParametersToParams($params, ['userId' => Users::class]);
        }

        return parent::getRoute($name, $params);
    }

    /**
     * Deletes all the sessions for a user.
     *
     * @param null $userId
     *
     * @return null
     * @throws CustomException
     * @throws MissingParametersException
     * @throws RouteException
     */
    public function deleteUserSessions($userId = null)
    {
        if (empty($userId)) {
            $lastChained = $this->getLatestChainedParameter([self::class]);

            $chainedResourceNames = array_keys($lastChained);
            $chainedResourceName  = (new $chainedResourceNames[0]($this->client))->resourceName;

            if ($chainedResourceName === 'users') {
                $userId = reset($lastChained);
            }
        }

        if (empty($userId)) {
            throw new MissingParametersException(__METHOD__, ['userId']);
        }

        return $this->client->delete($this->getRoute(__FUNCTION__, ['userId' => $userId]));
    }

    /**
     * Deletes the current session.
     *
     * @return null
     * @throws CustomException
     * @throws RouteException
     */
    public function logout()
    {
        return $this->client->delete($this->getRoute(__FUNCTION__));
    }

    /**
     * Shows the currently authenticated session
     *
     * @return \stdClass | null
     * @throws RouteException
     */
    public function current()
    {
        return $this->client->get($this->getRoute(__FUNCTION__));
    }
}
