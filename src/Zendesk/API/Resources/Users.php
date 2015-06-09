<?php

namespace Zendesk\API\Resources;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;
use Zendesk\API\HttpClient;

/**
 * The Users class exposes user management methods
 * Note: you must authenticate as a user!
 *
 * @package Zendesk\API
 */
class Users extends ResourceAbstract
{
    const OBJ_NAME = 'user';
    const OBJ_NAME_PLURAL = 'users';

    /**
     * @var UserIdentities
     */
    protected $identities;

    /**
     * @param HttpClient $client
     */
    public function __construct(\Zendesk\API\HttpClient $client)
    {
        parent::__construct($client);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'related' => 'users/{id}/related.json',
            'merge' => 'users/me/merge.json',
            'search' => 'users/search.json',
            'autocomplete' => 'users/autocomplete.json',
            'setPassword' => 'users/{id}/password.json',
            'changePassword' => 'users/{id}/password.json',
        ]);
    }

    /**
     * List all users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array())
    {
        if (isset($params['organization_id'])) {
            $this->endpoint = "organizations/{$params['organization_id']}/users.json";
        } elseif (isset($params['group_id'])) {
           $this->endpoint = 'groups/' . $params['group_id'] . '/users.json';
        } else {
            $this->endpoint = 'users.json';
        }

        return parent::findAll();
    }

    /**
     * Find many users
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findMany(array $params = array())
    {
        $params = $this->addChainedParametersToParams($params, ['ids' => get_class($this)]);
        $this->endpoint = 'users/show_many.json';

        $queryParams = ['ids' => implode(",", $params['ids'])];

        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $queryParams = array_merge($queryParams, $extraParams);

        $response = Http::send_with_options($this->client, $this->endpoint, ['queryParams' => $queryParams]);

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Find users by ids or external_ids
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function showMany(array $params = array())
    {
        if (isset($params['ids']) && isset($params['external_ids'])) {
            throw new \Exception('Only one parameter of ids or external_ids is allowed');
        } elseif (!isset($params['ids']) && !isset($params['external_ids'])) {
            throw new \Exception('Missing parameters ids or external_ids');
        } elseif (isset($params['ids']) && is_array($params['ids'])) {
            $this->endpoint = 'users/show_many.json';
            $queryParams = ['ids' => implode(',', $params['ids'])];
        } elseif (isset($params['external_ids']) && is_array($params['external_ids'])) {
            $this->endpoint = 'users/show_many.json';
            $queryParams = ['external_ids' => implode(',', $params['external_ids'])];
        } else {
            throw new \Exception('Parameters ids or external_ids must be arrays');
        }

        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $queryParams = array_merge($queryParams, $extraParams);

        $response = Http::send_with_options(
          $this->client,
          $this->endpoint,
          ['queryParams' => $queryParams]
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Get related information about the user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function related(array $params = array())
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);

        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }

        $queryParams = Http::prepareQueryParams($this->client->getSideload($params), $params);
        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__FUNCTION__, ['id' => $params['id']]),
          ['queryParams' => $queryParams]
        );

        $this->client->setSideload(null);

        return $response;
    }

//    /**
//     * Create a new user
//     *
//     * @param array $params
//     *
//     * @throws ResponseException
//     * @throws \Exception
//     *
//     * @return mixed
//     */
//    public function create(array $params)
//    {
//        $endPoint = Http::prepare('users.json');
//        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'POST');
//        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
//            throw new ResponseException(__METHOD__);
//        }
//        $this->client->setSideload(null);
//
//        return $response;
//    }

    /**
     * Merge the specified user (???)
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function merge(array $params = array())
    {
        $myId = $this->getChainedParameter(get_class($this));
        $mergeMe = !isset($myId) || is_null($myId);
        $hasKeys = $mergeMe ? array('email', 'password') : array('id');
        if (!$this->hasKeys($params, $hasKeys)) {
            throw new MissingParametersException(__METHOD__, $hasKeys);
        }

        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__FUNCTION__),
          ['postFields' => [self::OBJ_NAME => $params], 'method' => 'PUT']
        );
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Create multiple new users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function createMany(array $params)
    {
        $this->setRoute(__METHOD__, 'users/create_many.json');
        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__METHOD__),
          ['postFields' => [self::OBJ_NAME_PLURAL => $params], 'method' => 'POST']
        );

        $this->client->setSideload(null);

        return $response;
    }

//    /**
//     * Update a user
//     *
//     * @param $id
//     * @param array $updateResourceFields
//     *
//     * @return mixed
//     * @throws MissingParametersException
//     * @throws ResponseException
//     * @internal param array $params
//     *
//     */
//    public function update($id, array $updateResourceFields = [])
//    {
//        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
//
//        if (!$this->hasKeys($params, array('id'))) {
//            throw new MissingParametersException(__METHOD__, array('id'));
//        }
//        $id = $params['id'];
//        unset($params['id']);
//        $endPoint = Http::prepare('users/' . $id . '.json');
//        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'PUT');
//        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
//            throw new ResponseException(__METHOD__);
//        }
//        $this->client->setSideload(null);
//
//        return $response;
//    }

    /**
     * Update multiple users
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */

    public function updateMany(array $params)
    {
        if (!$this->hasKeys($params, array('ids'))) {
            throw new MissingParametersException(__METHOD__, array('ids'));
        }
        $ids = $params['ids'];
        unset($params['ids']);
        $this->setRoute(__METHOD__, 'users/update_many.json');
        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__METHOD__),
          ['postFields' => [self::OBJ_NAME => $params], 'method' => 'PUT']);
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Update multiple individual users
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */

    public function updateManyIndividualUsers(array $params)
    {
        $this->setRoute(__METHOD__, 'users/update_many.json');
        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__METHOD__),
          ['postFields' => [self::OBJ_NAME_PLURAL => $params], 'method' => 'PUT']
        );
        $this->client->setSideload(null);

        return $response;
    }


    /**
     * Suspend a user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     *
     * @return mixed
     */
    public function suspend(array $params = array())
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $params['suspended'] = true;

        return $this->update($params['id'], $params);
    }

//    /**
//     * Delete a user
//     *
//     * @param $id
//     *
//     * @return bool
//     * @throws MissingParametersException
//     * @throws ResponseException
//     *
//     */
//    public function delete($id)
//    {
//        if ($this->lastId != null) {
//            $params['id'] = $this->lastId;
//            $this->lastId = null;
//        }
//        if (!$this->hasKeys($params, array('id'))) {
//            throw new MissingParametersException(__METHOD__, array('id'));
//        }
//        $id = $params['id'];
//        $endPoint = Http::prepare('users/' . $id . '.json');
//        $response = Http::send($this->client, $endPoint, null, 'DELETE');
//        if ($this->client->getDebug()->lastResponseCode != 200) {
//            throw new ResponseException(__METHOD__);
//        }
//        $this->client->setSideload(null);
//
//        return true;
//    }

    /**
     * Search for users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function search(array $params)
    {
        $queryParams = isset($params['query']) ? ['query' => $params['query']] : [];
        $extraParams = Http::prepareQueryParams($this->client->getSideload($params), $params);

        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__FUNCTION__),
          ['queryParams' => array_merge($extraParams, $queryParams)]
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Requests autocomplete for users
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function autocomplete(array $params)
    {
        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__FUNCTION__),
          ['method' => 'POST', 'queryParams' => $params]
        );

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Update a user's profile image
     *
     * @param array $params
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function updateProfileImage(array $params)
    {
        if ($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if (!$this->hasKeys($params, array('id', 'file'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'file'));
        }
        if (!file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }
        $id = $params['id'];
        unset($params['id']);
        $endPoint = Http::prepare('users/' . $id . '.json');
        if (function_exists('curl_file_create')) {
            $response = Http::send($this->client, $endPoint, $params['file'], 'PUT',
                (isset($params['type']) ? $params['type'] : 'application/binary'));
        } else {
            $response = Http::send($this->client, $endPoint,
                array('user[photo][uploaded_data]' => '@' . $params['file']), 'PUT',
                (isset($params['type']) ? $params['type'] : 'application/binary'));
        }
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Show the current user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     *
     * @return mixed
     */
    public function me(array $params = array())
    {
        $params['id'] = 'me';

        return $this->find($params['id']);
    }

    /**
     * Sets a user's initial password
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function setPassword(array $params)
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (!$this->hasKeys($params, array('id', 'password'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'password'));
        }
        $id = $params['id'];
        unset($params['id']);

        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__FUNCTION__, ['id' => $id]),
          ['postFields' => [self::OBJ_NAME => $params], 'method' => 'POST']);

        $this->client->setSideload(null);

        return $response;
    }

    /**
     * Change a user's password
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function changePassword(array $params)
    {
        $params = $this->addChainedParametersToParams($params, ['id' => get_class($this)]);
        if (!$this->hasKeys($params, array('id', 'previous_password', 'password'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'previous_password', 'password'));
        }
        $id = $params['id'];
        unset($params['id']);

        $response = Http::send_with_options(
          $this->client,
          $this->getRoute(__FUNCTION__, ['id' => $id]),
          ['postFields' => $params, 'method' => 'PUT']
        );

        $this->client->setSideload(null);

        return $response;
    }

    /*
     * Syntactic sugar methods:
     * Handy aliases:
     */

    /**
     * @param int|null $id
     *
     * @return Tickets
     */
    public function tickets($id = null)
    {
        return ($id != null ? $this->client->tickets()->setLastId($id) : $this->client->tickets());
    }

    /**
     * @param int $id
     *
     * @return Tickets
     */
    public function ticket($id)
    {
        return $this->client->tickets()->setLastId($id);
    }

    /**
     * @param int|null $id
     *
     * @return UserIdentities
     */
    public function identities($id = null)
    {
        return ($id != null ? $this->identities->setLastId($id) : $this->identities);
    }

    /**
     * @param int $id
     *
     * @return UserIdentities
     */
    public function identity($id)
    {
        return $this->identities->setLastId($id);
    }

    /**
     * @param int|null $id
     *
     * @return Groups
     */
    public function groups($id = null)
    {
        return ($id != null ? $this->client->groups()->setLastId($id) : $this->client->groups());
    }

    /**
     * @param int $id
     *
     * @return Groups
     */
    public function group($id)
    {
        return $this->client->groups()->setLastId($id);
    }

    /**
     * @param int|null $id
     *
     * @return GroupMemberships
     */
    public function groupMemberships($id = null)
    {
        return ($id != null ? $this->client->groupMemberships()->setLastId($id) : $this->client->groupMemberships());
    }

    /**
     * @param int $id
     *
     * @return GroupMemberships
     */
    public function groupMembership($id)
    {
        return $this->client->groupMemberships()->setLastId($id);
    }

}
