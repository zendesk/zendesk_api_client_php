<?php

namespace Zendesk\API;

/**
 * Abstract class for all endpoints
 * @package Zendesk\API
 */
abstract class ClientAbstract
{

    /**
     * @var Client
     */
    protected $client;
    /**
     * @var int
     */
    protected $lastId;
    
    /**
     * @param Client $client
     */
     public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Saves an id for future methods in the chain
     *
     * @param int $id
     *
     * @return $this
     */
    public function setLastId($id) {
        $this->lastId = $id;
        return $this;
    }

    /**
     * Saves an id for future methods in the chain
     *
     * @return int
     */
    public function getLastId() {
        return $this->lastId;
    }

    /**
     * Check that all parameters have been supplied
     *
     * @param array $params
     * @param array $mandatory
     *
     * @return bool
     */
    public function hasKeys(array $params, array $mandatory) {
        for($i = 0; $i < count($mandatory); $i++) {
            if(!array_key_exists($mandatory[$i], $params)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check that any parameter has been supplied
     *
     * @param array $params
     * @param array $mandatory
     *
     * @return bool
     */
    public function hasAnyKey(array $params, array $mandatory) {
        for($i = 0; $i < count($mandatory); $i++) {
            if(array_key_exists($mandatory[$i], $params)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Enable side-loading (beta) - flags until the next chain
     *
     * @param array $fields
     *
     * @return $this
     */
    public function sideload(array $fields = array()) {
        $this->client->setSideload($fields);
        return $this;
    }

}
