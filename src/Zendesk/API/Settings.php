<?php

namespace Zendesk\API;

/**
 * The Settings class exposes methods for retrieving settings parameters
 * @package Zendesk\API
 */
class Settings extends ClientAbstract {

    const OBJ_NAME = 'settings';
    const OBJ_NAME_PLURAL = 'settings';

    /**
     * Returns a range of settings
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array ()) {
        $endPoint = Http::prepare('account/settings.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Update one or more settings
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function update(array $params) {
        $endPoint = Http::prepare('account/settings.json');
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params), 'PUT');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
