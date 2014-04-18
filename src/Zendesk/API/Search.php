<?php

namespace Zendesk\API;

/**
 * The Search class exposes methods defined in http://developer.zendesk.com/documentation/rest_api/search.html
 */
class Search extends ClientAbstract {

    /*
     * Perform a search
     */
    public function search(array $params) {
        if(!$this->hasKeys($params, array('query'))) {
            throw new MissingParametersException(__METHOD__, array('query'));
        }
        $endPoint = Http::prepare('search.json', null, $params);
        $response = Http::send($this->client, $endPoint, $params, 'GET');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

    /*
     * Perform an anonymous search
     */
    public function anonymousSearch(array $params) {
        if(!$this->hasKeys($params, array('query'))) {
            throw new MissingParametersException(__METHOD__, array('query'));
        }
        $endPoint = Http::prepare('portal/search.json', null, $params);
        $response = Http::send($this->client, $endPoint, $params, 'GET');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
