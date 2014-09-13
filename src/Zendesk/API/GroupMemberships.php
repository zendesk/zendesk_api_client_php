<?php

namespace Zendesk\API;

/**
 * The GroupMemberships class exposes group membership information
 * @package Zendesk\API
 */
class GroupMemberships extends ClientAbstract {

    const OBJ_NAME = 'group_membership';
    const OBJ_NAME_PLURAL = 'group_memberships';

    /**
     * List all group memberships
     *
     * @param array $params
     *
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
        if($this->client->groups()->getLastId() != null) {
            $params['group_id'] = $this->client->groups()->getLastId();
            $this->client->groups()->setLastId(null);
        }
        $endPoint = Http::prepare(
                (isset($params['assignable']) ? (isset($params['group_id']) ? 'groups/'.$params['group_id'].'/memberships/assignable.json' : 'group_memberships/assignable.json') : 
                (isset($params['user_id']) ? 'users/'.$params['user_id'].'/group_memberships.json' : 
                (isset($params['group_id']) ? 'groups/'.$params['group_id'].'/memberships.json' : 'group_memberships.json'))), $this->client->getSideload($params), $params
            );
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific group membership by id
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
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare((isset($params['user_id']) ? 'users/'.$params['user_id'].'/group_memberships/'.$params['id'].'.json' : 'group_memberships/'.$params['id'].'.json'), $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create a new group membership
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
        if($this->client->groups()->getLastId() != null) {
            $params['group_id'] = $this->client->groups()->getLastId();
            $this->client->groups()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('user_id', 'group_id'))) {
            throw new MissingParametersException(__METHOD__, array('user_id', 'group_id'));
        }
        $endPoint = Http::prepare((isset($params['user_id']) ? 'users/'.$params['user_id'].'/group_memberships.json' : 'group_memberships.json'));
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete a group membership
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
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare((isset($params['user_id']) ? 'users/'.$params['user_id'].'/group_memberships/'.$params['id'].'.json' : 'group_memberships/'.$params['id'].'.json'));
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

    /**
     * Make this group membership the default
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function makeDefault(array $params = array()) {
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
        $endPoint = Http::prepare('users/'.$params['user_id'].'/group_memberships/'.$params['id'].'/make_default.json');
        $response = Http::send($this->client, $endPoint, null, 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;    
    }

}
