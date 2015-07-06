<?php

namespace Zendesk\API\LiveTests;

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
        $this->subdomain    = getenv('SUBDOMAIN');
        $this->username     = getenv('USERNAME');
        $this->password     = getenv('PASSWORD');
        $this->token        = getenv('TOKEN');
        $this->oAuthToken   = getenv('OAUTH_TOKEN');
        $this->scheme       = getenv('SCHEME');
        $this->hostname     = getenv('HOSTNAME');
        $this->port         = getenv('PORT');
        $this->authStrategy = getenv('AUTH_STRATEGY');

        parent::__construct($name, $data, $dataName);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->client = new HttpClient($this->subdomain, $this->username, $this->scheme, $this->hostname, $this->port);

        $authOptions['username'] = $this->username;
        if ($this->authStrategy === 'basic') {
            $authOptions['token'] = $this->token;
        } else {
            $authOptions['token'] = $this->oAuthToken;
        }

        $this->client->setAuth($this->authStrategy, $authOptions);
    }
}
