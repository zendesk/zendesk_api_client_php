<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Zendesk\API\HttpClient;

/**
 * Basic test class
 */
abstract class BasicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpClient
     */
    protected $client;
    /**
     * @var string
     */
    protected $subdomain;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var string
     */
    protected $token;
    /**
     * @var string
     */
    protected $oAuthToken;
    /**
     * @var string
     */
    protected $hostname;
    /**
     * @var string
     */
    protected $scheme;
    /**
     * @var string
     */
    protected $port;
    /**
     * @var array
     */
    protected $mockedTransactionsContainer = [];

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->subdomain  = getenv('SUBDOMAIN');
        $this->username   = getenv('USERNAME');
        $this->token      = getenv('TOKEN');
        $this->oAuthToken = getenv('OAUTH_TOKEN');
        $this->scheme     = getenv('SCHEME');
        $this->hostname   = getenv('HOSTNAME');
        $this->port       = getenv('PORT');
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->client = new HttpClient($this->subdomain, $this->username, $this->scheme, $this->hostname, $this->port);
        $this->client->setAuth('oauth', ['token' => $this->oAuthToken]);
    }

    /**
     * This will mock the next responses sent via guzzle
     *
     * @param array $responses
     *   An array of GuzzleHttp\Psr7\Response objects
     */
    protected function mockApiResponses($responses = [])
    {
        if (empty($responses)) {
            return;
        } elseif (! is_array($responses)) {
            $responses = [$responses];
        }

        $history = Middleware::history($this->mockedTransactionsContainer);
        $mock    = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $this->client->guzzle = new Client(['handler' => $handler]);

    }

    /**
     * This checks the last request sent
     *
     * @param $options
     */
    public function assertLastRequestIs($options)
    {
        $this->assertRequestIs($options, count($this->mockedTransactionsContainer) - 1);
    }

    /**
     * This checks the response with the given index
     *
     * @param     $options
     * @param int $index
     */
    public function assertRequestIs($options, $index = 0)
    {
        $this->assertArrayHasKey($index, $this->mockedTransactionsContainer, 'Should have made an API call.');
        $transaction = $this->mockedTransactionsContainer[$index];
        $request     = $transaction['request'];
        $response    = $transaction['response'];

        $options = array_merge([
            'statusCode' => 200,
            'headers'    => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ], $options);

        $this->assertEquals($options['statusCode'], $response->getStatusCode());

        if (isset($options['headers']) && is_array($options['headers'])) {
            foreach ($options['headers'] as $headerKey => $value) {
                $this->assertNotEmpty($header = $request->getHeader($headerKey));
                $this->assertEquals($options['headers'][$headerKey], $value);
            }
        }

        if (isset($options['method'])) {
            $this->assertEquals($options['method'], $request->getMethod());
        }

        if (isset($options['endpoint'])) {
            // Truncate the `/api/v2` part of the target
            $endpoint = str_replace('/api/v2/', '', $request->getUri()->getPath());
            $this->assertEquals($options['endpoint'], $endpoint);
        }

        if (isset($options['queryParams'])) {
            $expectedQueryParams = urldecode(http_build_query($options['queryParams']));
            $this->assertEquals($expectedQueryParams, $request->getUri()->getQuery());
        }

        if (isset($options['postFields'])) {
            $this->assertEquals(json_encode($options['postFields']), $request->getBody()->getContents());
        }
    }
}
