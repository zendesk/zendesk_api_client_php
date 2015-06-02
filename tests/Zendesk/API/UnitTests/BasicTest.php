<?php

namespace Zendesk\API\UnitTests;

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

    public function __construct()
    {
        $this->subdomain = getenv('SUBDOMAIN');
        $this->username = getenv('USERNAME');
        $this->password = getenv('PASSWORD');
        $this->token = getenv('TOKEN');
        $this->oAuthToken = getenv('OAUTH_TOKEN');
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('token', $this->token);
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
