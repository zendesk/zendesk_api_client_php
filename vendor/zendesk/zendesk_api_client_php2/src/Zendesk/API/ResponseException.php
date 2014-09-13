<?php

namespace Zendesk\API;

/**
 * ResponseException extends the Exception class with simplified messaging
 */
class ResponseException extends \Exception {

    public function __construct($method, $detail = '', $code = 0, Exception $previous = null) {
        parent::__construct('Response to '.$method.' is not valid. Call $client->getDebug() for details'.$detail, $code, $previous);
    }

}
