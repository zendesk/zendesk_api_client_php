<?php

namespace Zendesk\API;

/**
 * The Tags class exposes methods as detailed on http://developer.zendesk.com/documentation/rest_api/tags.html
 * @package Zendesk\API
 */
class Tags extends ClientAbstract {

    const OBJ_NAME = 'tags';
    const OBJ_NAME_PLURAL = 'tags';

    /**
     * List the most popular tags
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array()) {
        $endPoint = Http::prepare('tags.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific tag
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
        if($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
        }
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if($this->client->organizations()->getLastId() != null) {
            $params['organization_id'] = $this->client->organizations()->getLastId();
            $this->client->organizations()->setLastId(null);
        }
        if(!$this->hasAnyKey($params, array('ticket_id', 'topic_id', 'organization_id'))) {
            throw new MissingParametersException(__METHOD__, array('ticket_id', 'topic_id', 'organization_id'));
        }
        $endPoint = Http::prepare(
                (isset($params['ticket_id']) ? 'tickets/'.$params['ticket_id'].'/tags.json' : 
                (isset($params['topic_id']) ? 'topics/'.$params['topic_id'].'/tags.json' : 
                (isset($params['organization_id']) ? 'organizations/'.$params['organization_id'].'/tags.json' : '')))
            );
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create a tag
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
        if($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
        }
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if($this->client->organizations()->getLastId() != null) {
            $params['organization_id'] = $this->client->organizations()->getLastId();
            $this->client->organizations()->setLastId(null);
        }
        if(!$this->hasAnyKey($params, array('ticket_id', 'topic_id', 'organization_id'))) {
            throw new MissingParametersException(__METHOD__, array('ticket_id', 'topic_id', 'organization_id'));
        }
        if(!$this->hasKeys($params, array('tags'))) {
            throw new MissingParametersException(__METHOD__, array('tags'));
        }
        $endPoint = Http::prepare(
                (isset($params['ticket_id']) ? 'tickets/'.$params['ticket_id'].'/tags.json' : 
                (isset($params['topic_id']) ? 'topics/'.$params['topic_id'].'/tags.json' : 
                (isset($params['organization_id']) ? 'organizations/'.$params['organization_id'].'/tags.json' : '')))
            );
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params['tags']), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Update a tag
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
        if($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
        }
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if($this->client->organizations()->getLastId() != null) {
            $params['organization_id'] = $this->client->organizations()->getLastId();
            $this->client->organizations()->setLastId(null);
        }
        if(!$this->hasAnyKey($params, array('ticket_id', 'topic_id', 'organization_id'))) {
            throw new MissingParametersException(__METHOD__, array('ticket_id', 'topic_id', 'organization_id'));
        }
        if(!$this->hasKeys($params, array('tags'))) {
            throw new MissingParametersException(__METHOD__, array('tags'));
        }
        $endPoint = Http::prepare(
                (isset($params['ticket_id']) ? 'tickets/'.$params['ticket_id'].'/tags.json' : 
                (isset($params['topic_id']) ? 'topics/'.$params['topic_id'].'/tags.json' : 
                (isset($params['organization_id']) ? 'organizations/'.$params['organization_id'].'/tags.json' : '')))
            );
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params['tags']), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete a tag
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function delete(array $params) {
        if($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
        }
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if($this->client->organizations()->getLastId() != null) {
            $params['organization_id'] = $this->client->organizations()->getLastId();
            $this->client->organizations()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('tags'))) {
            throw new MissingParametersException(__METHOD__, array('tags'));
        }
        if(!$this->hasAnyKey($params, array('ticket_id', 'topic_id', 'organization_id'))) {
            throw new MissingParametersException(__METHOD__, array('ticket_id', 'topic_id', 'organization_id'));
        }
        $endPoint = Http::prepare(
                (isset($params['ticket_id']) ? 'tickets/'.$params['ticket_id'].'/tags.json' : 
                (isset($params['topic_id']) ? 'topics/'.$params['topic_id'].'/tags.json' : 
                (isset($params['organization_id']) ? 'organizations/'.$params['organization_id'].'/tags.json' : '')))
            );
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params['tags']), 'DELETE');
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

    /**
     * @param array $params
     *
     * @throws ResponseException
     *
     * @return mixed
     */
    public function show(array $params = array()) { return $this->findAll($params); }

    /**
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     *
     * @return mixed
     */
    public function set(array $params) { return $this->create($params); }

    /**
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     *
     * @return mixed
     */
    public function add(array $params) { return $this->update($params); }

    /**
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     *
     * @return mixed
     */
    public function remove(array $params) { return $this->delete($params); }

}
