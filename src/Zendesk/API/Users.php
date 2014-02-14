<?php

namespace Zendesk\API;

/**
 * The Users class exposes user management methods
 * Note: you must authenticate as a user!
 */
class Users extends ClientAbstract {

    const OBJ_NAME = 'user';
    const OBJ_NAME_PLURAL = 'users';

    protected $identities;

    public function __construct($client) {
        parent::__construct($client);
        $this->identities = new UserIdentities($client);
    }

    /*
     * List all users
     */
    public function findAll(array $params = array()) {
        $endPoint = Http::prepare(
                (isset($params['organization_id']) ? 'organizations/'.$params['organization_id'].'/users' : 
                (isset($params['group_id']) ? 'groups/'.$params['group_id'].'/users' : 'users')
            ).'.json'.(isset($params['role']) ? (is_array($params['role']) ? '&role[]='.implode('&role[]=', $params['role']) : '?role='.$params['role']) : '').(isset($params['permission_set']) ? (isset($params['role']) ? '&' : '?').'permission_set='.$params['permission_set'] : ''), $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Show a specific user
     */
    public function find(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('users/'.$params['id'].'.json', $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Get related information about the user
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

    /*
     * Create a new user
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

    /*
     * Merge the specified user (???)
     */
    public function merge(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $id = $params['id'];
        unset($params['id']);
        $endPoint = Http::prepare('users/me/merge.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Create multiple new users
     */
    public function createMany(array $params) {
        $endPoint = Http::prepare('users/create_many.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Update a user
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

    /*
     * Suspend a user
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

    /*
     * Delete a user
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

    /*
     * Search for users
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

    /*
     * Requests autocomplete for users
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

    /*
     * Update a user's profile image
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
        $response = Http::send($this->client, $endPoint, array('user[photo][uploaded_data]' => '@'.$params['file']), 'PUT', (isset($params['type']) ? $params['type'] : 'application/binary'));
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Show the current user
     */
    public function me(array $params = array()) {
        $params['id'] = 'me';
        return $this->find($params);
    }

    /*
     * Sets a user's initial password
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

    /*
     * Change a user's password
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

    /*
     * Syntactic sugar methods:
     * Handy aliases:
     */
    public function tickets($id = null) { return ($id != null ? $this->client->tickets()->setLastId($id) : $this->client->tickets()); }
    public function ticket($id) { return $this->client->tickets()->setLastId($id); }
    public function identities($id = null) { return ($id != null ? $this->identities->setLastId($id) : $this->identities); }
    public function identity($id) { return $this->identities->setLastId($id); }
    public function groups($id = null) { return ($id != null ? $this->client->groups()->setLastId($id) : $this->client->groups()); }
    public function group($id) { return $this->client->groups()->setLastId($id); }
    public function groupMemberships($id = null) { return ($id != null ? $this->client->groupMemberships()->setLastId($id) : $this->client->groupMemberships()); }
    public function groupMembership($id) { return $this->client->groupMemberships()->setLastId($id); }

}
