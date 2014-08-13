<?php

namespace Zendesk\API;

/**
 * The TopicVotes class exposes topic subscription information
 * @package Zendesk\API
 */
class TopicVotes extends ClientAbstract {

    const OBJ_NAME = 'topic_vote';
    const OBJ_NAME_PLURAL = 'topic_votes';

    /**
     * List all topic votes
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
        $endPoint = Http::prepare((isset($params['topic_id']) ? 'topics/'.$params['topic_id'].'/votes.json' : 'users/'.$params['user_id'].'/topic_votes.json'), null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Check for a specific topic vote
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
        if(!$this->hasKeys($params, array('topic_id'))) {
            throw new MissingParametersException(__METHOD__, array('topic_id'));
        }
        $endPoint = Http::prepare('/topics/'.$params['topic_id'].'/vote.json'.(isset($params['user_id']) ? '?user_id='.$params['user_id'] : ''));
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || (($this->client->getDebug()->lastResponseCode != 200) && ($this->client->getDebug()->lastResponseCode != 404))) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create a new topic vote
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
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('topic_id'))) {
            throw new MissingParametersException(__METHOD__, array('topic_id'));
        }
        $endPoint = Http::prepare('topics/'.$params['topic_id'].'/vote.json'.(isset($params['user_id']) ? '?user_id='.$params['user_id'] : ''));
        $response = Http::send($this->client, $endPoint, null, 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Delete a topic vote
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
        if($this->client->topics()->getLastId() != null) {
            $params['topic_id'] = $this->client->topics()->getLastId();
            $this->client->topics()->setLastId(null);
        }
        if($this->client->users()->getLastId() != null) {
            $params['user_id'] = $this->client->users()->getLastId();
            $this->client->users()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('topic_id'))) {
            throw new MissingParametersException(__METHOD__, array('topic_id'));
        }
        $endPoint = Http::prepare('topics/'.$params['topic_id'].'/vote.json'.(isset($params['user_id']) ? '?user_id='.$params['user_id'] : ''));
        $response = Http::send($this->client, $endPoint, null, 'DELETE');
        if ($this->client->getDebug()->lastResponseCode != 200) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

}
