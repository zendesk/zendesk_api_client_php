<?php

namespace Zendesk\API;

/**
 * The Autocomplete class is as per http://developer.zendesk.com/documentation/rest_api/autocomplete.html
 * @package Zendesk\API
 */
class Autocomplete extends ClientAbstract {

    const OBJ_NAME = 'name';
    const OBJ_NAME_PLURAL = 'names';

    /**
     * Submits a request for matching tags
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return mixed
     */
    public function tags(array $params) {
        if(!$this->hasKeys($params, array('name'))) {
            throw new MissingParametersException(__METHOD__, array('name'));
        }
        $endPoint = Http::prepare('autocomplete/tags.json');
        $response = Http::send($this->client, $endPoint, array(self::OBJ_NAME => $params['name']), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
