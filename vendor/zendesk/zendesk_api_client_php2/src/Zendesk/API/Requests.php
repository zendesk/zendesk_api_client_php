<?php

namespace Zendesk\API;

/**
 * The Requests class exposes request management methods
 * Note: you must authenticate as a user!
 */
class Requests extends ClientAbstract {

    const OBJ_NAME = 'request';
    const OBJ_NAME_PLURAL = 'requests';

    /*
     * Public objects:
     */
    protected $comments;

    public function __construct($client) {
        parent::__construct($client);
        $this->comments = new RequestComments($client);
    }

    /*
     * List all requests
     */
    public function findAll(array $params = array()) {
        $endPoint = Http::prepare(
                (isset($params['organization_id']) ? 'organizations/'.$params['organization_id'].'/requests' : 
                (isset($params['user_id']) ? 'users/'.$params['user_id'].'/requests' : 
                (isset($params['ccd']) ? 'requests/ccd' : 
                (isset($params['solved']) ? 'requests/solved' : 
                (isset($params['open']) ? 'requests/open' : 'requests'))))
            ).'.json'.(isset($params['status']) ? '?status='.$params['status'] : ''), $this->client->getSideload($params), $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Show a specific request
     */
    public function find(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('requests/'.$params['id'].'.json', $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Create a new request
     */
    public function create(array $params) {
        $endPoint = Http::prepare('requests.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Update a request
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
        $endPoint = Http::prepare('requests/'.$id.'.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'PUT');
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
    public function comments($id = null) { return ($id != null ? $this->comments->setLastId($id) : $this->comments); }
    public function comment($id) { return $this->comments->setLastId($id); }
    public function open(array $params = array()) { $params['open'] = true; return $this->findAll($params); }
    public function solved(array $params = array()) { $params['solved'] = true; return $this->findAll($params); }
    public function ccd(array $params = array()) { $params['ccd'] = true; return $this->findAll($params); }

}
