<?php

namespace Zendesk\API;

/**
 * Abstract class for all endpoints
 */
abstract class ClientAbstract
{

    protected $client;
    protected $lastId;

    public function __construct($client) {
        $this->client = $client;
    }

    /*
     * Saves an id for future methods in the chain
     */
    public function setLastId($id) {
        $this->lastId = $id;
        return $this;
    }

    /*
     * Saves an id for future methods in the chain
     */
    public function getLastId() {
        return $this->lastId;
    }

    /*
     * Check that all parameters have been supplied
     */
    public function hasKeys($params, $mandatory) {
        for($i = 0; $i < count($mandatory); $i++) {
            if(!array_key_exists($mandatory[$i], $params)) {
                return false;
            }
        }
        return true;
    }

    /*
     * Check that any parameter has been supplied
     */
    public function hasAnyKey($params, $mandatory) {
        for($i = 0; $i < count($mandatory); $i++) {
            if(array_key_exists($mandatory[$i], $params)) {
                return true;
            }
        }
        return false;
    }

    /*
     * Enable side-loading (beta) - flags until the next chain
     */
    public function sideload(array $fields = array()) {
        $this->client->setSideload($fields);
        return $this;
    }

}
