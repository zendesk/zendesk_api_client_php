<?php

namespace Zendesk\API;

/**
 * The UserIdentities class exposes fields on the user profile page
 * @package Zendesk\API
 */
class UserIdentities extends ClientAbstract {

    const OBJ_NAME = 'identity';
    const OBJ_NAME_PLURAL = 'identities';

    /**
     * List all user identities
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array()) {
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('user_id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id'));
        }
        $endPoint = Http::prepare('users/'.$params['user_id'].'/identities.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific user identity
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
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('user_id', 'id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id', 'id'));
        }
        $endPoint = Http::prepare('users/'.$params['user_id'].'/identities/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create a new user identity
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params) {
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('user_id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id'));
        }
        $user_id = $params['user_id'];
        $prepare = (isset($params['end_user']) ? 'end_users' : 'users').'/'.$user_id.'/identities.json';
        unset($params['user_id']);
        unset($params['end_user']);
        $endPoint = Http::prepare($prepare);
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Mark a user identity as verified
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function markAsVerified(array $params = array()) {
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('user_id', 'id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id', 'id'));
        }
        $endPoint = Http::prepare('users/'.$params['user_id'].'/identities/'.$params['id'].'/verify.json');
        $response = Http::send($this->client, $endPoint, null, 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Mark a user identity as primary
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function makePrimary(array $params = array()) {
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('user_id', 'id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id', 'id'));
        }
        $endPoint = Http::prepare('users/'.$params['user_id'].'/identities/'.$params['id'].'/make_primary.json');
        $response = Http::send($this->client, $endPoint, null, 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Request verification
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function requestVerification(array $params = array()) {
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('user_id', 'id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id', 'id'));
        }
        $endPoint = Http::prepare('users/'.$params['user_id'].'/identities/'.$params['id'].'/request_verification.json');
        $response = Http::send($this->client, $endPoint, null, 'PUT');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete a user identity
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
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('user_id', 'id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id', 'id'));
        }
        $id = $params['id'];
        $endPoint = Http::prepare('users/'.$params['user_id'].'/identities/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

}
