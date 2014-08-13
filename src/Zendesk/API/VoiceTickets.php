<?php

namespace Zendesk\API;

/**
 * The VoiceTickets class exposes methods as outlined in http://developer.zendesk.com/documentation/rest_api/voice_integration.html
 * @package Zendesk\API
 */
class VoiceTickets extends ClientAbstract {

    /**
     * Create a voice or voicemail ticket
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $params) {
        $endPoint = Http::prepare('channels/voice/tickets.json');
        $response = Http::send($this->client, $endPoint, $params, 'POST'); // note: specify the whole package
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
