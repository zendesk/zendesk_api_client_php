<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Basic test class
 */
class BasicTest extends \PHPUnit_Framework_TestCase {

    protected $client;
    protected $subdomain;
    protected $password;
    protected $token;
    protected $oAuthToken;

    public function __construct() {
        $this->subdomain = getenv('SUBDOMAIN');
        $this->username = getenv('USERNAME');
        $this->password = getenv('PASSWORD');
        $this->token = getenv('TOKEN');
        $this->oAuthToken = getenv('OAUTH_TOKEN');
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('token', $this->token);
    }

    public function authTokenTest() {
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function credentialsTest() {
        $this->assertEquals(getenv('SUBDOMAIN') != '', true, 'Expecting getenv(\'SUBDOMAIN\') parameter; does phpunit.xml exist?');
        $this->assertEquals(getenv('TOKEN') != '', true, 'Expecting getenv(\'TOKEN\')parameter; does phpunit.xml exist?');
        $this->assertEquals(getenv('USERNAME') != '', true, 'Expecting getenv(\'USERNAME\') parameter; does phpunit.xml exist?');
    }
}

?>
