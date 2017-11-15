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
        $lastRequestHeaders  = $this->lastRequestHeaders;
        $lastResponseHeaders = $this->lastResponseHeaders;
        $lastResponseError   = $this->lastResponseError;

        if ($lastResponseError instanceof \Exception) {
            $lastResponseError = $this->lastResponseError->getMessage();
        } elseif (!is_string($lastResponseError)) {
            $lastResponseError = json_encode($lastResponseError);
        }

        if (!is_string($lastRequestHeaders)) {
            $lastRequestHeaders = json_encode($lastRequestHeaders);
        }
        if (!is_string($lastResponseHeaders)) {
            $lastResponseHeaders = json_encode($lastResponseHeaders);
        }

        return 'LastResponseCode: '.$this->lastResponseCode
               .', LastResponseError: '.$lastResponseError
               .', LastResponseHeaders: '.$lastResponseHeaders
               .', LastRequestHeaders: '.$lastRequestHeaders
               .', LastRequestBody: '.$this->lastRequestBody;
    }
}
