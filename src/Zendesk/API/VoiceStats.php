<?php

namespace Zendesk\API;

/**
 * The VoiceStats class exposes methods as outlined in http://developer.zendesk.com/documentation/rest_api/voice.html
 * @package Zendesk\API
 */
class VoiceStats extends ClientAbstract {

    /**
     * List all stats
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
        if(!$this->hasAnyKey($params, array('current_queue_activity', 'historical_queue_activity', 'agents_activity'))) {
            throw new MissingParametersException(__METHOD__, array('current_queue_activity', 'historical_queue_activity', 'agents_activity'));
        }
        $endPoint = Http::prepare(
                (isset($params['current_queue_activity']) ? 'channels/voice/stats/current_queue_activity.json' : 
                (isset($params['historical_queue_activity']) ? 'channels/voice/stats/historical_queue_activity.json' : 
                (isset($params['agents_activity']) ? 'channels/voice/stats/agents_activity.json' : ''))), null, $params
            );
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
