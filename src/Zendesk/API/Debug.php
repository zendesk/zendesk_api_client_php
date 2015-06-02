<?php

namespace Zendesk\API;

/**
 * Debug helper class
 * @package Zendesk\API
 */
class Debug
{

    /**
     * @var mixed
     */
    public $lastRequestHeaders;
    /**
     * @var mixed
     */
    public $lastResponseCode;
    /**
     * @var string
     */
    public $lastResponseHeaders;
    /**
     * @var mixed
     */
    public $lastResponseError;

    /**
     * @return string
     */
    public function __toString()
    {
        $lastError = $this->lastResponseError;
        if (!is_string($lastError)) {
            $lastError = json_encode($lastError);
        }
        $output = 'LastResponseCode: ' . $this->lastResponseCode
            . ', LastResponseError: ' . $lastError
            . ', LastResponseHeaders: ' . $this->lastResponseHeaders
            . ', LastRequestHeaders: ' . $this->lastRequestHeaders;

        return $output;
    }
}
