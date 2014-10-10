<?php

namespace Zendesk\API;

/**
 * The Organizations class exposes methods as detailed on http://developer.zendesk.com/documentation/rest_api/organizations.html
 */
class Organizations extends ClientAbstract {

    const OBJ_NAME = 'organization';
    const OBJ_NAME_PLURAL = 'organizations';

    /*
     * List all organizations
     */
    public function findAll(array $params = array()) {
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        $endPoint = Http::prepare((isset($params['user_id']) ? 'users/'.$params['user_id'].'/organizations.json' : 'organizations.json'), $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Show a specific organization
     */
    public function find(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('organizations/'.$params['id'].'.json', $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Create a new organization
     */
    public function create(array $params) {
        $endPoint = Http::prepare('organizations.json');
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Create multiple new organizations, returns as a job
     */
    public function createMany(array $params) {
        $endPoint = Http::prepare('organizations/create_many.json');
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Update an organization
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
        $endPoint = Http::prepare('organizations/'.$id.'.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Delete an organization
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
        $endPoint = Http::prepare('organizations/'.$id.'.json');
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

    /*
     * Autocomplete a list of organizations
     */
    public function autocomplete(array $params = array()) {
        if(!$this->hasKeys($params, array('name'))) {
            throw new MissingParametersException(__METHOD__, array('name'));
        }
        $endPoint = Http::prepare('organizations/autocomplete.json');
        $response = Http::send($this->client, $endPoint, array('name' => $params['name']), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

     /*
     * Get a list of related information
     */
    public function related(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('organizations/'.$params['id'].'/related.json', $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Search for organizations
     */
    public function search(array $params) {
        if(!$this->hasKeys($params, array('external_id'))) {
            throw new MissingParametersException(__METHOD__, array('external_id'));
        }
        $endPoint = Http::prepare('organizations/search.json', $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint, array('external_id' => $params['external_id']), 'GET');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
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
    public function tags($id = null) { return ($id != null ? $this->client->tags()->setLastId($id) : $this->client->tags()); }
    public function tag($id) { return $this->client->tags()->setLastId($id); }

}
