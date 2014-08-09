<?php

namespace Zendesk\API;

/**
 * The Twitter class exposes methods for managing and monitoring Twitter posts
 * @package Zendesk\API
 */
class Twitter extends ClientAbstract {

    const OBJ_NAME = 'monitored_twitter_handle';
    const OBJ_NAME_PLURAL = 'monitored_twitter_handles';

    /**
     * Return a list of monitored handles
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function handles(array $params = array()) {
        $endPoint = Http::prepare('channels/twitter/monitored_twitter_handles.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Responds with details of a specific handle
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function handleById(array $params) {
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('channels/twitter/monitored_twitter_handles/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
