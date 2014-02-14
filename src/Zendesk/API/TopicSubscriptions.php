<?php

namespace Zendesk\API;

/**
 * The TopicSubscriptions class exposes topic subscription information
 */
class TopicSubscriptions extends ClientAbstract {

    const OBJ_NAME = 'topic_subscription';
    const OBJ_NAME_PLURAL = 'topic_subscriptions';

    /*
     * List all topic subscriptions
     */
    public function findAll(array $params = array()) {
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        $endPoint = Http::prepare((isset($params['topic_id']) ? 'topics/'.$params['topic_id'].'/subscriptions.json' : 'topic_subscriptions.json'), null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Show a specific topic subscription
     */
    public function find(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('/topic_subscriptions/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Create a new topic subscription
     */
    public function create(array $params) {
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('topic_id', 'user_id'))) {
            throw new MissingParametersException(__METHOD__, array('topic_id', 'user_id'));
        }
        $endPoint = Http::prepare('topic_subscriptions.json');
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Delete a topic subscription
     */
    public function delete(array $params = array()) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('topic_subscriptions/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

}
