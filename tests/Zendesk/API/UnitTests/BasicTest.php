<?php

namespace Zendesk\API\UnitTests;

use Zendesk\API\HttpClient;
use \Aeris\GuzzleHttpMock\Mock as GuzzleHttpMock;


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
    protected $httpMock;

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
        $this->httpMock = new GuzzleHttpMock();
        $this->httpMock->attachToClient($this->client->guzzle);
    }

    protected function mockApiCall($httpMethod, $path, $response, $options = [])
    {
        $bodyParams = isset($options['bodyParams']) ? $options['bodyParams'] : [];
        $queryParams = isset($options['queryParams']) ? $options['queryParams'] : [];
        $statusCode = isset($options['statusCode']) ? $options['statusCode'] : [];

        $this->httpMock->shouldReceiveRequest()
            ->withMethod($httpMethod)
            ->withUrl($this->client->getApiUrl() . $path)
            ->withQueryParams($queryParams)
            ->withJsonBodyParams($bodyParams)
            ->andRespondWithJson($response, $statusCode = $statusCode);
    }

    public function authTokenTest()
    {
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function credentialsTest()
    {
        $this->assertNotEmpty($this->subdomain,
            'Expecting $this->subdomain parameter; does phpunit.xml exist?');
        $this->assertNotEmpty($this->token, 'Expecting $this->token parameter; does phpunit.xml exist?');
        $this->assertNotEmpty($this->username,
            'Expecting $this->username parameter; does phpunit.xml exist?');
    }
}

