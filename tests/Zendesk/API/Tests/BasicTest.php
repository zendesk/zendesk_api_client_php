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
}

?>
