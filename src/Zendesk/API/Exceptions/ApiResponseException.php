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
        $message = $response->getReasonPhrase();

        $level = floor($response->getStatusCode() / 100);
        // Check if business-level error message
        // https://developer.zendesk.com/rest_api/docs/core/introduction#requests
        if ($response->getHeaderLine('Content-Type') == 'application/json; charset=UTF-8') {
            $responseBody = json_decode($response->getBody()->getContents());

            $this->errorDetails = $responseBody->details;
            $message = $responseBody->description . "\n" . 'Errors: ' . print_r($this->errorDetails, true);
        } elseif ($level == '5') {
            $message = 'Zendesk may be experiencing internal issues or undergoing scheduled maintenance.';
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
