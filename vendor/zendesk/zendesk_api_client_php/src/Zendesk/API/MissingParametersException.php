<?php

namespace Zendesk\API;

/**
 * MissingParametersException extends the Exception class with simplified messaging
 */
class MissingParametersException extends \Exception {

    public function __construct($method, array $params, $code = 0, Exception $previous = null) {
        parent::__construct('Missing parameters: \''.implode("', '", $params).'\' must be supplied for '.$method, $code, $previous);
    }

}
