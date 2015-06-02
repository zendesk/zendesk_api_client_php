<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

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

        $this->client = new Client($this->subdomain, $this->username, $this->scheme, $this->hostname, $this->port);
        $this->client->setAuth('token', $this->token);
    }

    protected function mockApiCall($httpMethod, $path, $response, $code = 200)
    {
        $this->http->mock
            ->when()
                ->methodIs($httpMethod)
                ->pathIs('/api/v2' . $path)
            ->then()
                ->body(json_encode($response))
                ->statusCode($code)
            ->end();
        $this->http->setUp();
    }

    public function setUp()
    {
        $this->setUpHttpMock();
        parent::setUp();
    }

    public function tearDown()
    {
        $this->tearDownHttpMock();
        parent::tearDown();
    }

    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass(getenv("PORT"), getenv("HOSTNAME"));
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        static::tearDownHttpMockAfterClass();
        parent::tearDownAfterClass();
    }

    public function authTokenTest()
    {
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function credentialsTest()
    {
        $this->assertEquals($this->subdomain != '', true,
            'Expecting $this->subdomain parameter; does phpunit.xml exist?');
        $this->assertEquals($this->token != '', true, 'Expecting $this->token parameter; does phpunit.xml exist?');
        $this->assertEquals($this->username != '', true,
            'Expecting $this->username parameter; does phpunit.xml exist?');
    }
}
