<?php

namespace Zendesk\API;

/**
 * MissingParametersException extends the Exception class with simplified messaging
 * @package Zendesk\API
 */
class MissingParametersException extends \Exception {
  
    /**
     * @param string     $method
     * @param array      $params
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($method, array $params, $code = 0, \Exception $previous = null) {
        parent::__construct('Missing parameters: \''.implode("', '", $params).'\' must be supplied for '.$method, $code, $previous);
    }

}
