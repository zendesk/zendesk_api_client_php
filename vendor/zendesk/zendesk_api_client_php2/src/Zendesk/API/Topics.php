<?php

namespace Zendesk\API;

/**
 * The Topics class exposes topic information
 */
class Topics extends ClientAbstract {

    const OBJ_NAME = 'topic';
    const OBJ_NAME_PLURAL = 'topics';

    protected $comments;
    protected $subscriptions;
    protected $votes;

    public function __construct($client) {
        parent::__construct($client);
        $this->comments = new TopicComments($client);
        $this->subscriptions = new TopicSubscriptions($client);
        $this->votes = new TopicVotes($client);
    }

    /*
     * List all topics
     */
    public function findAll(array $params = array()) {
        if($this->client->forums()->getLastId() != null) {
            $params['forum_id'] = $this->client->forums()->getLastId();
            $this->client->forums()->setLastId(null);
        }
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        $endPoint = Http::prepare(
                (isset($params['forum_id']) ? 'forums/'.$params['forum_id'].'/topics.json' : 
                (isset($params['user_id']) ? 'users/'.$params['user_id'].'/topics.json' : 'topics.json')), $this->client->getSideload($params), $params
            );
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Show a specific topic
     */
    public function find(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare((is_array($params['id']) ? 'topics/show_many.json?ids='.implode(',', $params['id']) : 'topics/'.$params['id'].'.json'), $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Create a new topic
     */
    public function create(array $params) {
        if($this->client->forums()->getLastId() != null) {
            $params['forum_id'] = $this->client->forums()->getLastId();
            $this->client->forums()->setLastId(null);
        }
        $endPoint = Http::prepare('topics.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Import a topic (same as create but without notifications)
     */
    public function import(array $params) {
        $endPoint = Http::prepare('import/topics.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Update a topic
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
        $endPoint = Http::prepare('topics/'.$id.'.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Delete a topic
     */
    public function delete(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('topics/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

    /*
     * Generic method to object getter
     */
    public function __call($name, $arguments) {
        if(isset($this->$name)) {
            return ((isset($arguments[0])) && ($arguments[0] != null) ? $this->$name->setLastId($arguments[0]) : $this->$name);
        }
        $namePlural = $name.'s'; // try pluralize
        if(isset($this->$namePlural)) {
            return $this->$namePlural->setLastId($arguments[0]);
        } else {
            throw new CustomException("No method called $name available in ".__CLASS__);
        }
    }

    public function tags($id = null) { return ($id != null ? $this->client->tags()->setLastId($id) : $this->client->tags()); }
    public function tag($id) { return $this->client->tags()->setLastId($id); }

}
