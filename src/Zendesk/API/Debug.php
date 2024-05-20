<?php

namespace Zendesk\API;

/**
 * Debug helper class
 */
class Debug
{
    /**
     * @var mixed
     */
    public $lastRequestBody;
    /**
     * @var mixed
     */
    public $lastRequestHeaders;
    /**
     * @var mixed
     */
    public $lastResponseCode;
    /**
     * @var mixed
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
        if (! is_string($lastError)) {
            $lastError = json_encode($lastError);
        }
        $output = 'LastResponseCode: ' . $this->lastResponseCode
                  . ', LastResponseError: ' . $lastError
                  . ', LastResponseHeaders: ' . json_encode($this->lastResponseHeaders)
                  . ', LastRequestHeaders: ' . json_encode($this->lastRequestHeaders)
                  . ', LastRequestBody: ' . $this->lastRequestBody;

        return $output;
    }
}
