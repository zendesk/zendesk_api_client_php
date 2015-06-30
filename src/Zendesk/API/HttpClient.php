<?php

namespace Zendesk\API;

/*
 * Dead simple autoloader:
 * spl_autoload_register(function($c){@include 'src/'.preg_replace('#\\\|_(?!.+\\\)#','/',$c).'.php';});
 */

use Zendesk\API\Exceptions\AuthException;
use Zendesk\API\Resources\Automations;
use Zendesk\API\Resources\Macros;
use Zendesk\API\Resources\Tags;
use Zendesk\API\Resources\Tickets;
use Zendesk\API\Resources\Users;
use Zendesk\API\Resources\Views;
use Zendesk\API\UtilityTraits\InstantiatorTrait;

/**
 * Client class, base level access
 * @package Zendesk\API
 * @method Debug debug()
 * @method Tickets ticket()
 * @method Tickets tickets()
 * @method Views views()
 * @method Users users()
 */
class HttpClient
{
    use InstantiatorTrait;

    const AUTH_OAUTH = 'oauth';
    const AUTH_BASIC = 'basic';

    /**
     * @var string
     */
    protected $authStrategy;
    /**
     * @var Array
     */
    protected $authOptions;
    /**
     * @var string
     */
    protected $subdomain;
    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $scheme;
    /**
     * @var string
     */
    protected $hostname;
    /**
     * @var integer
     */
    protected $port;
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
    protected $apiUrl;
    /**
     * @var string
     */
    protected $apiVer = 'v2';
    /**
     * @var array|null
     */
    protected $sideload;

    // Properties
    /**
     * @var Tickets
     */
    protected $tickets;
    /**
     * @var Users
     */
    protected $users;
    /**
     * @var Views
     */
    protected $views;
    /**
     * @var Macros
     */
    protected $macros;
    /**
     * @var Automations
     */
    protected $automations;
    /**
     * @var Debug
     */
    protected $debug;
    /**
     * @var \GuzzleHttp\Client
     */
    public $guzzle;

    /**
     * @param string $subdomain
     * @param string $username
     */

    public function __construct(
        $subdomain,
        $username,
        $scheme = "https",
        $hostname = "zendesk.com",
        $port = 443,
        $guzzle = null
    ) {
        if (is_null($guzzle)) {
            $this->guzzle = new \GuzzleHttp\Client();
        } else {
            $this->guzzle = $guzzle;
        }

        $this->subdomain = $subdomain;
        $this->username  = $username;
        $this->hostname  = $hostname;
        $this->scheme    = $scheme;
        $this->port      = $port;

        if (empty($subdomain)) {
            $this->apiUrl = "$scheme://$hostname:$port/api/{$this->apiVer}/";
        } else {
            $this->apiUrl = "$scheme://$subdomain.$hostname:$port/api/{$this->apiVer}/";
        }

        $this->debug = new Debug();
    }

    public static function getValidRelations()
    {
        return [
            'tickets'     => Tickets::class,
            'users'       => Users::class,
            'views'       => Views::class,
            'tags'        => Tags::class,
            'macros'      => Macros::class,
            'automations' => Automations::class,
        ];
    }

    /**
     * Configure the authorization method
     *
     * @param $strategy
     * @param array $options
     *
     * @throws AuthException
     */
    public function setAuth($strategy, array $options)
    {
        $validAuthStrategies = [self::AUTH_BASIC, self::AUTH_OAUTH];
        if (! in_array($strategy, $validAuthStrategies)) {
            throw new AuthException('Invalid auth strategy set, please use `'
                                    . implode('` or `', $validAuthStrategies)
                                    . '`');
        }

        $this->authStrategy = $strategy;

        if ($strategy == self::AUTH_BASIC) {
            if (! array_key_exists('username', $options) || ! array_key_exists('token', $options)) {
                throw new AuthException('Please supply `username` and `token` for basic auth.');
            }
        } elseif ($strategy == self::AUTH_OAUTH) {
            if (! array_key_exists('token', $options)) {
                throw new AuthException('Please supply `token` for oauth.');
            }
        }

        $this->authOptions = $options;
    }

    /**
     * Returns the supplied subdomain
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Returns the generated api URL
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Returns a text value indicating the type of authorization configured
     * @return string
     */
    public function getAuthOptions()
    {
        return $this->authOptions;
    }

    /**
     * Returns the authentication strategy set
     * @return string
     */
    public function getAuthStrategy()
    {
        return $this->authStrategy;
    }

    /**
     * Set debug information as an object
     *
     * @param mixed $lastRequestHeaders
     * @param mixed $lastResponseCode
     * @param string $lastResponseHeaders
     * @param mixed $lastResponseError
     */
    public function setDebug($lastRequestHeaders, $lastResponseCode, $lastResponseHeaders, $lastResponseError)
    {
        $this->debug->lastRequestHeaders  = $lastRequestHeaders;
        $this->debug->lastResponseCode    = $lastResponseCode;
        $this->debug->lastResponseHeaders = $lastResponseHeaders;
        $this->debug->lastResponseError   = $lastResponseError;
    }

    /**
     * Returns debug information in an object
     * @return Debug
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Sideload setter
     *
     * @param array|null $fields
     *
     * @return HttpClient
     */
    public function setSideload(array $fields = null)
    {
        $this->sideload = $fields;

        return $this;
    }

    /**
     * Sideload getter
     *
     * @param array|null $params
     *
     * @return array|null
     */
    public function getSideload(array $params = null)
    {
        if ((isset($params['sideload'])) && (is_array($params['sideload']))) {
            return $params['sideload'];
        } else {
            return $this->sideload;
        }
    }

    /*
     * These ones don't follow the usual construct
     */

    /**
     * @param int $id
     *
     * @return $this
     */
    public function category($id)
    {
        return $this->categories->setLastId($id);
    }

    /**
     * @param int|null $id
     *
     * @return ActivityStream
     */
    public function activities($id = null)
    {
        return ($id != null ? $this->activityStream()->setLastId($id) : $this->activityStream());
    }

    /**
     * @param int $id
     *
     * @return ActivityStream
     */
    public function activity($id)
    {
        return $this->activityStream()->setLastId($id);
    }

    /**
     * @param int $id
     *
     * @return JobStatuses
     */
    public function jobStatus($id)
    {
        return $this->jobStatuses()->setLastId($id);
    }

    /**
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @return mixed
     */
    public function search(array $params)
    {
        return $this->search->performSearch($params);
    }

    /**
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @return mixed
     */
    public function anonymousSearch(array $params)
    {
        return $this->search->anonymousSearch($params);
    }
}
