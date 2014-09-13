<?php

namespace Zendesk\API;

/**
 * The CustomRoles class exposes access to custom roles
 * @package Zendesk\API
 */
class CustomRoles extends ClientAbstract {

    const OBJ_NAME = 'custom_role';
    const OBJ_NAME_PLURAL = 'custom_roles';

    /**
     * List all custom roles
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function findAll(array $params = array()) {
        $endPoint = Http::prepare('custom_roles.json', null, $params);
        $response = Http::send($this->client, $endPoint);
        if ((!is_object($response)) || ($this->client->getDebug()->lastResponseCode != 200)) {
            throw new ResponseException(__METHOD__);
        }
        $this->client->setSideload(null);
        return $response;
    }

}
