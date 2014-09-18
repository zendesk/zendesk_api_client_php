<?php

namespace Zendesk\API;

/**
 * CustomException extends the Exception class with simplified messaging
 */
class CustomException extends \Exception {

    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
