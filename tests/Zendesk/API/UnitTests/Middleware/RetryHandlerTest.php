<?php

namespace Zendesk\API\UnitTests\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Zendesk\API\Middleware\RetryHandler;
use Zendesk\API\UnitTests\BasicTest;

class RetryHandlerTest extends BasicTest
{
    /**
     * Checks that only the exceptions to retry are those listed with the assumption
     * that retry_if will return false
     *
     * @param \Exception $exception
     * @param bool $success
     *
     * @dataProvider requestExceptionsProvider
     */
    public function testExceptionsRetry($exception, $success)
    {
        $config = [
            'max' => 1,
            'exceptions' => [ServerException::class, ClientException::class],
            'retry_if' => function () {
                return false;
            }
        ];
        $client = $this->mockApiResponses([
            new $exception('', new Request('GET', '')),
            new Response(200),
        ], ['handlers' => [
            new RetryHandler($config)
        ]]);

        $this->checkRequest($client, $success, $exception);
    }

    /**
     * Samples for testExceptionsRetry
     *
     * @return array
     */
    public function requestExceptionsProvider()
    {
        return [
            [ServerException::class, true],
            [ClientException::class, true],
            [ConnectException::class, false],
            [TooManyRedirectsException::class, false]
        ];
    }

    /**
     * Checks that the max number of retries behaves properly
     *
     * @param int $limit
     * @param bool $success
     *
     * @dataProvider retryLimitProvider
     */
    public function testRetryLimit($limit, $success)
    {
        $config = [
            'max' => $limit
        ];

        $responses = [];
        do {
            $responses[] = new ConnectException('', new Request('GET', ''));
        } while (count($responses) < 10);
        $responses[] = new Response(200);
        $client = $this->mockApiResponses($responses, ['handlers' => [
            new RetryHandler($config)
        ]]);

        $this->checkRequest($client, $success);
    }

    /**
     * Samples for testRetryLimit
     *
     * @return array
     */
    public function retryLimitProvider()
    {
        return [
            [-10, false], // negative value should not retry requests
            [0, false], // zero value should not retry requests
            [5, false], // value lesser than the number of errors should fail
            [10, true], // value equal to the number of errors should eventually succeed on the request
            [12, true] // value greater than the number of errors should eventually succeed on the request
        ];
    }

    /**
     * Checks that the retry_if is used to decide the retry
     *
     * @param callable $retryIf
     * @param bool $success
     *
     * @dataProvider retryIfProvider
     */
    public function testRetryIf($retryIf, $success)
    {
        $config = [
            'max' => 1,
            'retry_if' => $retryIf
        ];

        $client = $this->mockApiResponses([
            new Response(500),
            new Response(200)
        ], ['handlers' => [
            new RetryHandler($config)
        ]]);

        $this->checkRequest($client, $success, ServerException::class);
    }

    /**
     * Samples for testRetryIf
     *
     * @return array
     */
    public function retryIfProvider()
    {
        return [
            // check if retry_if is called with appropriate parameters
            [function ($retries, $request, $response, $exception) {
                return $request instanceof Request &&
                $response instanceof Response &&
                $response->getStatusCode() == 500 &&
                is_null($exception);
            }, true],

            // check if retry_if is really used to decide the retry
            [function () {
                return false;
            }, false]
        ];
    }

    /**
     * Checks that the delay between retries is correctly computed
     *
     * @param int $maxInterval maximum interval
     * @param int $backoffFactor backoff factor
     * @param int $shouldConsume expected consumed time
     * @param string $message
     *
     * @dataProvider retryDelayProvider
     */
    public function testRetryDelay($maxInterval, $backoffFactor, $shouldConsume, $message)
    {
        $config = [
            'max' => 3,
            'interval' => 100,
            'max_interval' => $maxInterval,
            'backoff_factor' => $backoffFactor,
        ];
        $i = 0;
        $responses = [];
        do {
            $responses[] = new ConnectException($i++, new Request('GET', ''));
        } while (count($responses) < 3);
        $responses[] = new Response(200);
        $client = $this->mockApiResponses($responses, ['handlers' => [
            new RetryHandler($config)
        ]]);

        $start = microtime(true);
        $client->get('/');
        $timeConsumed = round(microtime(true) - $start, 3) * 1000;
        // round to the nearest 100 to remove noise from executing other statements
        $this->assertEquals($shouldConsume, round($timeConsumed, -2), $message);
    }

    /**
     * Samples for testRetryDelay
     *
     * @return array
     */
    public function retryDelayProvider()
    {
        return [
            [20000, 1, 300, 'for each request delays should be 100ms'], // all delays are 100ms
            [20000, 2, 1400, 'delay should have an exponential growth'], // for each retry delays are 200, 400, 800 ms
            [1000, 3, 2200, 'delay should not exceed max interval'] // for each retry delays are 300, 900, 1000 ms
        ];
    }

    /**
     * Tests that by default the Zendesk\API\HttpClient retries
     * requests that failed because of ssl issue
     */
    public function testHttpClientRetry()
    {
        $this->setUp();
        $config = $this->client->guzzle->getConfig();
        $sslException = new RequestException(
            'ssl',
            $this->getMockBuilder(Request::class)
                ->disableOriginalConstructor()
                ->getMock()
        );

        $mock = new MockHandler([
            $sslException,
            $sslException,
            new Response()
        ]);

        $config['handler']->setHandler(HandlerStack::create($mock));
        $client = new Client($config);
        $response = $client->get('/');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Checks if the request on the client will be successful or not
     *
     * @param Client $client
     * @param bool $success
     * @param \Exception $exception
     */
    private function checkRequest(Client $client, $success, $exception = ConnectException::class)
    {
        if (!$success) {
            $this->setExpectedException($exception);
        }

        $response = $client->get('/');

        if ($success) {
            $this->assertEquals(200, $response->getStatusCode());
        }
    }
}
