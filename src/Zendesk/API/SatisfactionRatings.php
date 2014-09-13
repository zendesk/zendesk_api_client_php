<?php

namespace Zendesk\API;

/**
 * The SatisfactionRatings class exposes methods as detailed on http://developer.zendesk.com/documentation/rest_api/satisfaction_ratings.html
 * @package Zendesk\API
 */
class SatisfactionRatings extends ClientAbstract {

    const OBJ_NAME = 'satisfaction_rating';
    const OBJ_NAME_PLURAL = 'satisfaction_ratings';

    /**
     * List all satisfaction ratings
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array()) {
        if($this->client->tickets()->getLastId() != null) {
            $params['ticket_id'] = $this->client->tickets()->getLastId();
            $this->client->tickets()->setLastId(null);
        }
        $endPoint = Http::prepare('satisfaction_ratings.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific satisfaction rating
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
        if($this->lastId != null) {
            $params['id'] = $this->lastId;
            $this->lastId = null;
        }
        if(!$this->hasKeys($params, array('id'))) {
            throw new MissingParametersException(__METHOD__, array('id'));
        }
        $endPoint = Http::prepare('satisfaction_ratings/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Create a new satisfaction rating (authorised end user credentials only please!)
     *
     * @param array $params
     *
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
        $endPoint = Http::prepare('tickets/'.$params['ticket_id'].'/satisfaction_rating.json');
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__, ($this->client->getDebug()->lastResponseCode == 403 ? ' (hint: you need to authenticate as a verified end user for this method)' : ''));
        }
        $this->client->setSideload(null);
        return $response;
    }

}
