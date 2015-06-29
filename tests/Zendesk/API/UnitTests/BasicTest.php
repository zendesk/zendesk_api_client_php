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
    protected $client;
    protected $subdomain;
    protected $password;
    protected $token;
    protected $oAuthToken;
    protected $hostname;
    protected $scheme;
    protected $port;
    protected $mockedTransactionsContainer = [];

    public function __construct()
    {
        $this->subdomain = getenv('SUBDOMAIN');
        $this->username = getenv('USERNAME');
        $this->password = getenv('PASSWORD');
        $this->token = getenv('TOKEN');
        $this->oAuthToken = getenv('OAUTH_TOKEN');
        $this->scheme = getenv('SCHEME');
        $this->hostname = getenv('HOSTNAME');
        $this->port = getenv('PORT');
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        $this->client = new HttpClient($this->subdomain, $this->username, $this->scheme, $this->hostname, $this->port);
        $this->client->setAuth('token', $this->token);
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
        } elseif (!is_array($responses)) {
            $responses = [$responses];
        }

        $history = Middleware::history($this->mockedTransactionsContainer);
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $this->client->guzzle = new Client(['handler' => $handler]);
    }

    public function authTokenTest()
    {
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function credentialsTest()
    {
        $this->assertNotEmpty(
            $this->subdomain,
            'Expecting $this->subdomain parameter; does phpunit.xml exist?'
        );
        $this->assertNotEmpty($this->token, 'Expecting $this->token parameter; does phpunit.xml exist?');
        $this->assertNotEmpty(
            $this->username,
            'Expecting $this->username parameter; does phpunit.xml exist?'
        );
    }

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
        $transaction = $this->mockedTransactionsContainer[$index];
        $request = $transaction['request'];
        $response = $transaction['response'];

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
