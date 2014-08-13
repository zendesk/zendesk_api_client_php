<?php

namespace Zendesk\API;

/**
 * The Locales class exposes view management methods
 * @package Zendesk\API
 */
class Locales extends ClientAbstract {

    const OBJ_NAME = 'locale';
    const OBJ_NAME_PLURAL = 'locales';

    /**
     * List all locales
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array()) {
        $endPoint = Http::prepare(
                (isset($params['current']) ? 'locales/current.json' : 
                (isset($params['agent']) ? 'locales/agent.json' : 'locales.json')), null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Show a specific locale
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
        $endPoint = Http::prepare('locales/'.$params['id'].'.json');
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /**
     * Detect the best locale from the supplied list
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function detectBest(array $params) {
        if(!$this->hasKeys($params, array('available_locales'))) {
            throw new MissingParametersException(__METHOD__, array('available_locales'));
        }
        $endPoint = Http::prepare('locales/detect_best_locale.json', null, $params);
        $response = Http::send($this->client, $endPoint, array('available_locales' => $params['available_locales']), 'GET');
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

    /**
     * @throws ResponseException
     *
     * @return mixed
     */
    public function agent() { return $this->findAll(array('agent' => true)); }

    /**
     * @throws ResponseException
     *
     * @return mixed
     */
    public function current() { return $this->findAll(array('current' => true)); }

}
