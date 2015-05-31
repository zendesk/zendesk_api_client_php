<?php

namespace Zendesk\API;

/**
 * The Users class exposes user management methods
 * Note: you must authenticate as a user!
 *
 * @package Zendesk\API
 */
class Users extends ClientAbstract {

    const OBJ_NAME = 'user';
    const OBJ_NAME_PLURAL = 'users';

    /**
     * @var UserIdentities
     */
    protected $identities;

    /**
     * @param Client $client
     */
    public function __construct(Client $client) {
        parent::__construct($client);
        $this->identities = new UserIdentities($client);
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
    public function findAll(array $params = array()) {
        $endPoint = Http::prepare(
                (isset($params['organization_id']) ? 'organizations/'.$params['organization_id'].'/users' :
                (isset($params['group_id']) ? 'groups/'.$params['group_id'].'/users' : 'users')
            ).'.json'.(isset($params['role']) ? (is_array($params['role']) ? '?role[]='.implode('&role[]=', $params['role']) : '?role='.$params['role']) : '').(isset($params['permission_set']) ? (isset($params['role']) ? '&' : '?').'permission_set='.$params['permission_set'] : ''), $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function find(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare((is_array($params['id']) ? 'users/show_many.json?ids='.implode(',', $params['id']) : 'users/'.$params['id'].'.json'), $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
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
    public function showMany(array $params = array()) {
        if (isset($params['ids']) && isset($params['external_ids'])) {
            throw new \Exception('Only one parameter of ids or external_ids is allowed');
        } elseif (!isset($params['ids']) && !isset($params['external_ids'])) {
            throw new \Exception('Missing parameters ids or external_ids');
        } elseif (isset($params['ids']) && is_array($params['ids'])) {
            $path = 'users/show_many.json?ids='.implode(',', $params['ids']);
        } elseif (isset($params['external_ids']) && is_array($params['external_ids'])) {
            $path = 'users/show_many.json?external_ids='.implode(',', $params['external_ids']);
        } else {
            throw new \Exception('Parameters ids or external_ids must be arrays');
        }

        $endPoint = Http::prepare($path, $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
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
    public function related(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('users/'.$params['id'].'/related.json', $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create a new user
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params) {
        $endPoint = Http::prepare('users.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

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
        if ($this->lastId != null) {
            $myId = $this->lastId;
            $this->lastId = null;
        }
        $mergeMe = !isset($myId) || is_null($myId);
        $hasKeys = $mergeMe ? array('email', 'password') : array('id');
        if (!$this->hasKeys($params, $hasKeys)) {
            throw new MissingParametersException(__METHOD__, $hasKeys);
        }
        $endPoint = Http::prepare('users/' . ($mergeMe ? 'me' : $myId) . '/merge.json');
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
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
    public function createMany(array $params) {
        $endPoint = Http::prepare('users/create_many.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME_PLURAL => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Update a user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function update(array $params) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        unset($params['id']);
        $endPoint = Http::prepare('users/'.$id.'.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

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

    public function updateMany(array $params) {
        if(!$this->hasKeys($params, array('ids'))) {
            throw new MissingParametersException(__METHOD__, array('ids'));
        }
        $ids = $params['ids'];
        unset($params['ids']);
        $endPoint = Http::prepare('users/update_many.json?ids='.$ids);            
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
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

    public function updateManyIndividualUsers(array $params) {
        $endPoint = Http::prepare('users/update_many.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME_PLURAL => $params), 'PUT');

        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
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
    public function suspend(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $params['suspended'] = true;
        return $this->update($params);
    }

    /**
     * Delete a user
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return bool
     */
    public function delete(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('users/'.$id.'.json');
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

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
    public function search(array $params) {
        $endPoint = Http::prepare('users/search.json?'.http_build_query($params), $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
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
    public function autocomplete(array $params) {
        $endPoint = Http::prepare('users/autocomplete.json?'.http_build_query($params));
        $response = Http::send($this->client, $endPoint, null, 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
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
    public function updateProfileImage(array $params) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id', 'file'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'file'));
        }
        if(!file_exists($params['file'])) {
            throw new CustomException('File '.$params['file'].' could not be found in '.__METHOD__);
        }
        $id = $params['id'];
        unset($params['id']);
        $endPoint = Http::prepare('users/'.$id.'.json');
        if (function_exists('curl_file_create')) {
            $response = Http::send($this->client, $endPoint, $params['file'], 'PUT', (isset($params['type']) ? $params['type'] : 'application/binary'));
        }else {
            $response = Http::send($this->client, $endPoint, array('user[photo][uploaded_data]' => '@'.$params['file']), 'PUT', (isset($params['type']) ? $params['type'] : 'application/binary'));
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
    public function me(array $params = array()) {
        $params['id'] = 'me';
        return $this->find($params);
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
    public function setPassword(array $params) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id', 'password'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'password'));
        }
        $id = $params['id'];
        unset($params['id']);
        $endPoint = Http::prepare('users/'.$id.'/password.json');
        $response = Http::send($this->client, $endPoint, $params, 'POST');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
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
    public function changePassword(array $params) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id', 'previous_password', 'password'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'previous_password', 'password'));
        }
        $id = $params['id'];
        unset($params['id']);
        $endPoint = Http::prepare('users/'.$id.'/password.json');
        $response = Http::send($this->client, $endPoint, $params, 'PUT');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Incremental user updates with a supplied start_time. 
     * 
     * @link https://developer.zendesk.com/rest_api/docs/core/incremental_export
     *
     * @param array $params
     *  int start_time Timestamp indicating the minimum updated_at of the users to retrieve.
     *  
     * @param boolean $sample Retrieve just a sample (for debugging/testing).
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * 
     * @todo DRY up this copy of {@see Tickets#incremental}
     *
     * @return mixed
     */
    public function incremental(array $params, $sample = false)
    {
        return Tickets::incremental($params, $sample);
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
    public function tickets($id = null) { return ($id != null ? $this->client->tickets()->setLastId($id) : $this->client->tickets()); }

    /**
     * @param int $id
     *
     * @return Tickets
     */
    public function ticket($id) { return $this->client->tickets()->setLastId($id); }

    /**
     * @param int|null $id
     *
     * @return UserIdentities
     */
    public function identities($id = null) { return ($id != null ? $this->identities->setLastId($id) : $this->identities); }

    /**
     * @param int $id
     *
     * @return UserIdentities
     */
    public function identity($id) { return $this->identities->setLastId($id); }

    /**
     * @param int|null $id
     *
     * @return Groups
     */
    public function groups($id = null) { return ($id != null ? $this->client->groups()->setLastId($id) : $this->client->groups()); }

    /**
     * @param int $id
     *
     * @return Groups
     */
    public function group($id) { return $this->client->groups()->setLastId($id); }

    /**
     * @param int|null $id
     *
     * @return GroupMemberships
     */
    public function groupMemberships($id = null) { return ($id != null ? $this->client->groupMemberships()->setLastId($id) : $this->client->groupMemberships()); }

    /**
     * @param int $id
     *
     * @return GroupMemberships
     */
    public function groupMembership($id) { return $this->client->groupMemberships()->setLastId($id); }

}
