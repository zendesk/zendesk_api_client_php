<?php

namespace Zendesk\API\Exceptions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

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
        $message = $e->getMessage();

        if ($e instanceof ClientException) {
            $response           = $e->getResponse();
            $responseBody       = $response->getBody()->getContents();
            $this->errorDetails = $responseBody;
            $message .= ' [details] ' . $this->errorDetails;
        } elseif ($e instanceof ServerException) {
            $message .= ' [details] Zendesk may be experiencing internal issues or undergoing scheduled maintenance.';
        } elseif (! $e->hasResponse()) {
            $request = $e->getRequest();
            // Unsuccessful response, log what we can
            $message .= ' [url] ' . $request->getUri();
            $message .= ' [http method] ' . $request->getMethod();
            $message .= ' [body] ' . $request->getBody()->getContents();
        }

        parent::__construct($message, $e->getCode(), $e);
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
