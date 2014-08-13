<?php

namespace Zendesk\API;

/**
 * The TopicComments class exposes topic commentary information
 * @package Zendesk\API
 */
class TopicComments extends ClientAbstract {

    const OBJ_NAME = 'topic_comment';
    const OBJ_NAME_PLURAL = 'topic_comments';

    /**
     * List all topic comments
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
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if(!$this->hasAnyKey($params, array('topic_id', 'user_id'))) {
            throw new MissingParametersException(__METHOD__, array('topic_id', 'user_id'));
        }
        $endPoint = Http::prepare(
                (isset($params['topic_id']) ? 'topics/'.$params['topic_id'].'/comments.json' : 
                (isset($params['user_id']) ? 'users/'.$params['user_id'].'/topic_comments.json' : '')), $this->client->getSideload($params), $params
            );
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific topic comment
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
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if(!$this->hasAnyKey($params, array('topic_id', 'user_id'))) {
            throw new MissingParametersException(__METHOD__, array('topic_id', 'user_id'));
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare((isset($params['topic_id']) ? 'topics/'.$params['topic_id'].'/comments/'.$params['id'].'.json' : 'users/'.$params['user_id'].'/topic_comments/'.$params['id'].'.json'), $this->client->getSideload($params));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create a new topic comment
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
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('topic_id'))) {
            throw new MissingParametersException(__METHOD__, array('topic_id'));
        }
        $endPoint = Http::prepare('topics/'.$params['topic_id'].'/comments.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Import a topic comment (same as create but without notifications)
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function import(array $params) {
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('topic_id'))) {
            throw new MissingParametersException(__METHOD__, array('topic_id'));
        }
        $endPoint = Http::prepare('import/topics/'.$params['topic_id'].'/comments.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Update a topic comment
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
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('id', 'topic_id'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'topic_id'));
        }
        $prepare = 'topics/'.$params['topic_id'].'/comments/'.$params['id'].'.json';
        unset($params['id']);
        unset($params['topic_id']);
        $endPoint = Http::prepare($prepare);
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete a topic comment
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
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('id', 'topic_id'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'topic_id'));
        }
        $endPoint = Http::prepare('topics/'.$params['topic_id'].'/comments/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

}
