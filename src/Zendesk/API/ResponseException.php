<?php

namespace Zendesk\API;

/**
 * ResponseException extends the Exception class with simplified messaging
 * @package Zendesk\API
 */
class ResponseException extends \Exception {
  
    /**
     * @param string     $method
     * @param string     $detail
     * @param int        $code
     * @param \Exception $previous
     */
        public function __construct($method, $detail = '', $code = 0, \Exception $previous = null) {
        parent::__construct('Response to '.$method.' is not valid. Call $client->getDebug() for details'.$detail, $code, $previous);
    }

}
