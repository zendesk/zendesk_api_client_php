<?php

namespace Zendesk\API;

/**
 * The RequestComments class exposes request comment management methods
 * Note: you must authenticate as a user!
 *
 * @package Zendesk\API
 */
class RequestComments extends ClientAbstract {

    const OBJ_NAME = 'comment';
    const OBJ_NAME_PLURAL = 'comments';

    /**
     * Get comments from a request
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
        if($this->client->requests()->getLastId() != null) {
            $params['request_id'] = $this->client->requests()->getLastId();
            $this->client->requests()->setLastId(null);
        }
        if(!$this->hasKeys($params, array('request_id'))) {
            throw new MissingParametersException(__METHOD__, array('request_id'));
        }
        $endPoint = Http::prepare('requests/'.$params['request_id'].'/comments.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific request
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
        if($this->client->requests()->getLastId() != null) {
            $params['request_id'] = $this->client->requests()->getLastId();
            $this->client->requests()->setLastId(null);
        }
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id', 'request_id'))) {
            throw new MissingParametersException(__METHOD__, array('id', 'request_id'));
        }
        $endPoint = Http::prepare('requests/'.$params['request_id'].'/comments/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
