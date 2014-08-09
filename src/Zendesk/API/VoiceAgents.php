<?php

namespace Zendesk\API;

/**
 * The VoiceAgents class exposes methods as outlined in http://developer.zendesk.com/documentation/rest_api/voice_integration.html
 * @package Zendesk\API
 */
class VoiceAgents extends ClientAbstract {

    /**
     * Opens a user's profile in an agent's browser
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return bool
     */
    public function openUserProfile(array $params = array()) {
        if(!$this->hasKeys($params, array('agent_id', 'user_id'))) {
            throw new MissingParametersException(__METHOD__, array('agent_id', 'user_id'));
        }
        $endPoint = Http::prepare('channels/voice/agents/'.$params['agent_id'].'/users/'.$params['user_id'].'/display.json');
        $response = Http::send($this->client, $endPoint, null, 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

    /**
     * Opens a ticket in an agent's browser
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     *
     * @return bool
     */
    public function openTicket(array $params = array()) {
        if(!$this->hasKeys($params, array('agent_id', 'ticket_id'))) {
            throw new MissingParametersException(__METHOD__, array('agent_id', 'ticket_id'));
        }
        $endPoint = Http::prepare('channels/voice/agents/'.$params['agent_id'].'/tickets/'.$params['ticket_id'].'/display.json');
        $response = Http::send($this->client, $endPoint, null, 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return true;
    }

}
