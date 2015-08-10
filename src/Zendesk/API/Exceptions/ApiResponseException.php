<?php

namespace Zendesk\API\Exceptions;

use GuzzleHttp\Exception\RequestException;

/**
 * Class ApiResponseException
 *
 * @package Zendesk\API\Exceptions
 */
class ApiResponseException extends \Exception
{
    /**
     * @var array
     */
    protected $errorDetails = [];

    public function __construct(RequestException $e)
    {
        $response = $e->getResponse();
        $message  = $response->getReasonPhrase()
            . " [status code] " . $response->getStatusCode();

        $level = floor($response->getStatusCode() / 100);
        // Check if business-level error message
        // https://developer.zendesk.com/rest_api/docs/core/introduction#requests
        if ($level == '4') {
            $responseBody = $response->getBody()->getContents();
            $this->errorDetails = $responseBody;
            $message .= ' [details] ' . $this->errorDetails;
        } elseif ($level == '5') {
            $message .= ' [details] Zendesk may be experiencing internal issues or undergoing scheduled maintenance.';
        }

        parent::__construct($message, $response->getStatusCode());
    }

    /**
     * Returns an array of error fields with descriptions.
     *
     * {
     *   "email": [{
     *       "description": "Email: roge@example.org is already being used by another user",
     *       "error": "DuplicateValue"
     *   }],
     *   "external_id":[{
     *       "description": "External has already been taken",
     *       "error": "DuplicateValue"
     *   }]
     * }
     *
     * @return array
     */
    public function getErrorDetails()
    {
        return $this->errorDetails;
    }
}
