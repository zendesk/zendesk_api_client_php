<?php

namespace Zendesk\API;

/**
 * The SharingAgreements class exposes methods as detailed at http://developer.zendesk.com/documentation/rest_api/sharing_agreements.html
 * @package Zendesk\API
 */
class SharingAgreements extends ClientAbstract {

    const OBJ_NAME = 'sharing_agreement';
    const OBJ_NAME_PLURAL = 'sharing_agreements';

    /**
     * Returns a list of sharing agreements
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array ()) {
        $endPoint = Http::prepare('sharing_agreements.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
