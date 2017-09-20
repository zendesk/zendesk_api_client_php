<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\StreamInterface;
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
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->subdomain  = getenv('SUBDOMAIN');
        $this->username   = getenv('USERNAME');
        $this->token      = getenv('TOKEN');
        $this->oAuthToken = getenv('OAUTH_TOKEN');
        $this->scheme     = getenv('SCHEME');
        $this->hostname   = getenv('HOSTNAME');
        $this->port       = getenv('PORT');

        parent::__construct($name, $data, $dataName);
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
     * @param array $config
     *   config for the GuzzleHttp\Client
     */
    protected function mockApiResponses($responses = [], array $config = [])
    {
        if (empty($responses)) {
            return;
        } elseif (! is_array($responses)) {
            $responses = [$responses];
        }

        $history = Middleware::history($this->mockedTransactionsContainer);
        $mock    = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        if (isset($config['handlers'])) {
            foreach ($config['handlers'] as $handler) {
                $handlerStack->push($handler);
            }
        }
        $config['handler'] = $handlerStack;

        return $this->client->guzzle = new Client($config);
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

        $options = array_merge(
            [
                'statusCode' => 200,
                'headers'    => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'apiBasePath' => '/api/v2/',
            ],
            $options
        );

        $this->assertEquals($options['statusCode'], $response->getStatusCode());

        if (isset($options['multipart'])) {
            $body = $request->getBody();
            $this->assertInstanceOf(MultipartStream::class, $body);
            $this->assertGreaterThan(0, $body->getSize());
            $this->assertNotEmpty($header = $request->getHeaderLine('Content-Type'));
            $this->assertContains('multipart/form-data', $header);
            unset($options['headers']['Content-Type']);
        }

        if (isset($options['file'])) {
            $body = $request->getBody();
            $this->assertInstanceOf(StreamInterface::class, $body);
            $this->assertGreaterThan(0, $body->getSize());
            $this->assertNotEmpty($header = $request->getHeaderLine('Content-Type'));
            $this->assertEquals('application/binary', $header);
            if ($options['file'] instanceof StreamInterface) {
                $this->assertEquals($options['file']->getMetadata('uri'), $body->getMetadata('uri'));
            } else {
                $this->assertEquals($options['file'], $body->getMetadata('uri'));
            }
            unset($options['headers']['Content-Type']);
        }

        if (isset($options['headers']) && is_array($options['headers'])) {
            foreach ($options['headers'] as $headerKey => $value) {
                if ($value) {
                    $this->assertNotEmpty($header = $request->getHeaderLine($headerKey));
                    $this->assertEquals($value, $header);
                }
            }
        }

        if (isset($options['method'])) {
            $this->assertEquals($options['method'], $request->getMethod());
        }

        if (isset($options['apiBasePath'])) {
            $this->assertSame(
                0,
                strpos($request->getUri()->getPath(), $options['apiBasePath']),
                "Failed asserting that the API basepath is {$options['apiBasePath']}"
            );
        }

        if (isset($options['endpoint'])) {
            // Truncate the base path from the target, this was added since the existing usage pattern for
            // $options['endpoint'] does not include the api/v2 base path
            $endpoint = preg_replace('/^' . preg_quote($options['apiBasePath'], '/') . '/', '', $request->getUri()->getPath());
            $this->assertEquals($options['endpoint'], $endpoint);
        }

        if (isset($options['queryParams'])) {
            $expectedQueryParams = urldecode(http_build_query($options['queryParams']));
            $this->assertEquals($expectedQueryParams, $request->getUri()->getQuery());
        }

        if (isset($options['postFields'])) {
            $this->assertEquals(json_encode($options['postFields']), $request->getBody()->getContents());
        }

        if (isset($options['requestUri'])) {
            $this->assertEquals($options['requestUri'], $request->getUri()->__toString());
        }
    }

    /**
     * Test for the endpoint using the given method and endpoint
     *
     * @param        $userFunction
     * @param        $endpoint - An array containing [request method, endpoint path]
     * @param string $method
     */
    protected function assertEndpointCalled($userFunction, $endpoint, $method = 'GET', $additionalAsserts = [])
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        call_user_func($userFunction);

        $this->assertLastRequestIs(
            array_merge($additionalAsserts, [
                'method'   => $method,
                'endpoint' => $endpoint,
            ])
        );
    }
}
