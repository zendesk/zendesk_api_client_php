<?php

namespace Zendesk\API;

/**
 * The TicketImport class exposes import methods for tickets
 * @package Zendesk\API
 */
class TicketImport extends ClientAbstract {

    const OBJ_NAME = 'ticket';
    const OBJ_NAME_PLURAL = 'tickets';

    /**
     * Create a new ticket field
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function import(array $params) {
        $endPoint = Http::prepare('imports/tickets.json');
        $response = Http::send($this->client, $endPoint, array (self::OBJ_NAME => $params), 'POST');
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 201)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
