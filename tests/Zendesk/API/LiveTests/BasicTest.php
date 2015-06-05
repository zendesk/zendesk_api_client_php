<?php

namespace Zendesk\API\LiveTests;

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

        $this->client = new HttpClient($this->subdomain, $this->username, $this->scheme, $this->hostname, $this->port);
        $this->client->setAuth('token', $this->token);
        $this->httpMock = new GuzzleHttpMock();
    }

    protected function mockApiCall($httpMethod, $path, $response, $options = array())
    {
        $this->httpMock->attachToClient($this->client->guzzle);

        $options = array_merge(array(
            'code' => 200,
            'timesCalled' => 1
        ), $options);

        $this->httpMock->shouldReceiveRequest()
            ->withMethod($httpMethod)
            ->withUrl($this->client->getApiUrl() . $path)
            ->withJsonBodyParams($options['postFields'])
            ->andRespondWithJson($response, $statusCode = $options['code']);

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
