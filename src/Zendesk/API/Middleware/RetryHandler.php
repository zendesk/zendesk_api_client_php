<?php

namespace Zendesk\API\Middleware;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RetryMiddleware;

class RetryHandler
{
    /**
     * @var array $timeoutCodes list of timeout status codes: Request Timeout, Authentication Timeout, Gateway Timeout
     */
    private $timeoutCodes = [408, 419, 504];

    private $options = [
        'max' => 2, // limit of retries
        'interval' => 300, // base delay between retries, unit is in milliseconds
        'max_interval' => 20000, // maximum delay value
        'backoff_factor' => 1, // backoff factor
        'exceptions' => [ConnectException::class], // Exceptions to retry without checking retry_if
        'retry_if' => null, // callable function that can decide whether to retry the request or not
    ];

    /**
     * RetryHandler constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->options = array_merge($this->options, $config);
    }

    /**
     * Returns the function that will decide whether to retry the request or not.
     *
     * @return callable
     */
    public function shouldRetryRequest()
    {
        return function ($retries, Request $request, $response, $exception) {
            if ($retries >= $this->options['max']) {
                return false;
            } elseif ($this->isRetryableException($exception)) {
                return true;
            } elseif (is_callable($this->options['retry_if'])) {
                return call_user_func($this->options['retry_if'], $retries, $request, $response, $exception);
            }

            return $response && in_array($response->getStatusCode(), $this->timeoutCodes);
        };
    }

    /**
     * Returns the function that computes the delay before the next retry
     *
     * @return callable
     */
    public function delay()
    {
        return function ($retries) {
            $current_interval = $this->options['interval'] * pow($this->options['backoff_factor'], $retries);
            $current_interval = min([$current_interval, $this->options['max_interval']]);

            return $current_interval;
        };
    }

    /**
     * Called when the middleware is handled by the client.
     *
     * @param callable $handler
     *
     * @return RetryMiddleware
     */
    public function __invoke(callable $handler)
    {
        $retryMiddleware = new RetryMiddleware($this->shouldRetryRequest(), $handler, $this->delay());

        return $retryMiddleware;
    }

    /**
     * Checks if the exception thrown warrants a retry
     *
     * @param $exception
     *
     * @return bool
     */
    private function isRetryableException($exception)
    {
        if (!$this->options['exceptions']) {
            return true;
        }

        foreach ($this->options['exceptions'] as $expectedException) {
            if ($exception instanceof $expectedException) {
                return true;
            }
        }

        return false;
    }
}
