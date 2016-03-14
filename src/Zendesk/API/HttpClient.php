<?php

namespace Zendesk\API;

/*
 * Dead simple autoloader:
 * spl_autoload_register(function($c){@include 'src/'.preg_replace('#\\\|_(?!.+\\\)#','/',$c).'.php';});
 */

use Zendesk\API\Exceptions\AuthException;
use Zendesk\API\Resources\Core\Activities;
use Zendesk\API\Resources\Core\AppInstallations;
use Zendesk\API\Resources\Core\Apps;
use Zendesk\API\Resources\Core\Attachments;
use Zendesk\API\Resources\Core\AuditLogs;
use Zendesk\API\Resources\Core\Autocomplete;
use Zendesk\API\Resources\Core\Automations;
use Zendesk\API\Resources\Core\Bookmarks;
use Zendesk\API\Resources\Core\Brands;
use Zendesk\API\Resources\Core\CustomRoles;
use Zendesk\API\Resources\Core\DynamicContent;
use Zendesk\API\Resources\Core\GroupMemberships;
use Zendesk\API\Resources\Core\Groups;
use Zendesk\API\Resources\Core\Incremental;
use Zendesk\API\Resources\Core\JobStatuses;
use Zendesk\API\Resources\Core\Locales;
use Zendesk\API\Resources\Core\Macros;
use Zendesk\API\Resources\Core\OAuthClients;
use Zendesk\API\Resources\Core\OAuthTokens;
use Zendesk\API\Resources\Core\OrganizationFields;
use Zendesk\API\Resources\Core\OrganizationMemberships;
use Zendesk\API\Resources\Core\Organizations;
use Zendesk\API\Resources\Core\OrganizationSubscriptions;
use Zendesk\API\Resources\Core\PushNotificationDevices;
use Zendesk\API\Resources\Core\Requests;
use Zendesk\API\Resources\Core\SatisfactionRatings;
use Zendesk\API\Resources\Core\Search;
use Zendesk\API\Resources\Core\Sessions;
use Zendesk\API\Resources\Core\SharingAgreements;
use Zendesk\API\Resources\Core\SlaPolicies;
use Zendesk\API\Resources\Core\SupportAddresses;
use Zendesk\API\Resources\Core\SuspendedTickets;
use Zendesk\API\Resources\Core\Tags;
use Zendesk\API\Resources\Core\Targets;
use Zendesk\API\Resources\Core\TicketFields;
use Zendesk\API\Resources\Core\TicketImports;
use Zendesk\API\Resources\Core\Tickets;
use Zendesk\API\Resources\Core\Triggers;
use Zendesk\API\Resources\Core\TwitterHandles;
use Zendesk\API\Resources\Core\UserFields;
use Zendesk\API\Resources\Core\Users;
use Zendesk\API\Resources\Core\Views;
use Zendesk\API\Resources\HelpCenter;
use Zendesk\API\Resources\Voice;
use Zendesk\API\Traits\Utility\InstantiatorTrait;
use Zendesk\API\Utilities\Auth;

/**
 * Client class, base level access
 *
 * @method Activities activities()
 * @method Apps apps()
 * @method Attachments attachments()
 * @method AuditLogs auditLogs()
 * @method Automations automations()
 * @method Bookmarks bookmarks()
 * @method Debug debug()
 * @method DynamicContent dynamicContent()
 * @method Groups groups()
 * @method Incremental incremental()
 * @method Locales locales()
 * @method Macros macros()
 * @method OAuthClients oauthClients()
 * @method OrganizationFields organizationFields()
 * @method Organizations organizations()
 * @method PushNotificationDevices pushNotificationDevices()
 * @method Requests requests()
 * @method Search search()
 * @method Sessions sessions()
 * @method SatisfactionRatings satisfactionRatings()
 * @method SharingAgreements sharingAgreements()
 * @method SlaPolicies slaPolicies()
 * @method SupportAddresses supportAddresses()
 * @method SuspendedTickets suspendedTickets()
 * @method Tags tags()
 * @method Targets targets()
 * @method Tickets tickets()
 * @method TicketImports ticketImports()
 * @method TwitterHandles twitterHandles()
 * @method Triggers triggers()
 * @method UserFields userFields()
 * @method Users users()
 * @method Views views()
 *
 */
class HttpClient
{
    const VERSION = '2.0.0';

    use InstantiatorTrait;

    /**
     * @var array $headers
     */
    private $headers = [];

    /**
     * @var Auth
     */
    protected $auth;
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
    protected $apiUrl;
    /**
     * @var string
     */
    protected $apiVer = 'v2';
    /**
     * @var array|null
     */
    protected $sideload;
    /**
     * @var Debug
     */
    protected $debug;

    /**
     * @var \GuzzleHttp\Client
     */
    public $guzzle;
    /**
     * @var HelpCenter
     */
    public $helpCenter;
    /**
     * @var Voice
     */
    public $voice;

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

        $this->debug      = new Debug();
        $this->helpCenter = new HelpCenter($this);
        $this->voice      = new Voice($this);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getValidSubResources()
    {
        return [
            'apps'                      => Apps::class,
            'activities'                => Activities::class,
            'appInstallations'          => AppInstallations::class,
            'attachments'               => Attachments::class,
            'auditLogs'                 => AuditLogs::class,
            'autocomplete'              => Autocomplete::class,
            'automations'               => Automations::class,
            'bookmarks'                 => Bookmarks::class,
            'brands'                    => Brands::class,
            'customRoles'               => CustomRoles::class,
            'dynamicContent'            => DynamicContent::class,
            'groupMemberships'          => GroupMemberships::class,
            'groups'                    => Groups::class,
            'incremental'               => Incremental::class,
            'jobStatuses'               => JobStatuses::class,
            'locales'                   => Locales::class,
            'macros'                    => Macros::class,
            'oauthClients'              => OAuthClients::class,
            'oauthTokens'               => OAuthTokens::class,
            'organizationFields'        => OrganizationFields::class,
            'organizationMemberships'   => OrganizationMemberships::class,
            'organizations'             => Organizations::class,
            'organizationSubscriptions' => OrganizationSubscriptions::class,
            'pushNotificationDevices'   => PushNotificationDevices::class,
            'requests'                  => Requests::class,
            'satisfactionRatings'       => SatisfactionRatings::class,
            'sharingAgreements'         => SharingAgreements::class,
            'search'                    => Search::class,
            'slaPolicies'               => SlaPolicies::class,
            'sessions'                  => Sessions::class,
            'supportAddresses'          => SupportAddresses::class,
            'suspendedTickets'          => SuspendedTickets::class,
            'tags'                      => Tags::class,
            'targets'                   => Targets::class,
            'tickets'                   => Tickets::class,
            'ticketFields'              => TicketFields::class,
            'ticketImports'             => TicketImports::class,
            'triggers'                  => Triggers::class,
            'twitterHandles'            => TwitterHandles::class,
            'userFields'                => UserFields::class,
            'users'                     => Users::class,
            'views'                     => Views::class,
        ];
    }

    /**
     * @return Auth
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Configure the authorization method
     *
     * @param       $strategy
     * @param array $options
     *
     * @throws AuthException
     */
    public function setAuth($strategy, array $options)
    {
        $this->auth = new Auth($strategy, $options);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return HttpClient
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Return the user agent string
     *
     * @return string
     */
    public function getUserAgent()
    {
        return 'ZendeskAPI PHP ' . self::VERSION;
    }

    /**
     * Returns the supplied subdomain
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Returns the generated api URL
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Set debug information as an object
     *
     * @param mixed  $lastRequestHeaders
     * @param mixed  $lastResponseCode
     * @param string $lastResponseHeaders
     * @param mixed  $lastResponseError
     */
    public function setDebug(
        $lastRequestHeaders,
        $lastRequestBody,
        $lastResponseCode,
        $lastResponseHeaders,
        $lastResponseError
    ) {
        $this->debug->lastRequestHeaders  = $lastRequestHeaders;
        $this->debug->lastRequestBody     = $lastRequestBody;
        $this->debug->lastResponseCode    = $lastResponseCode;
        $this->debug->lastResponseHeaders = $lastResponseHeaders;
        $this->debug->lastResponseError   = $lastResponseError;
    }

    /**
     * Returns debug information in an object
     *
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
    public function getSideload(array $params = [])
    {
        // Allow both for backward compatability
        $sideloadKeys = array('include', 'sideload');

        if (! empty($sideloads = array_intersect_key($params, array_flip($sideloadKeys)))) {
            // Merge to a single array
            return call_user_func_array('array_merge', $sideloads);
        } else {
            return $this->sideload;
        }
    }

    public function get($endpoint, $queryParams = [])
    {
        $sideloads = $this->getSideload($queryParams);

        if (is_array($sideloads)) {
            $queryParams['include'] = implode(',', $sideloads);
            unset($queryParams['sideload']);
        }

        $response = Http::send(
            $this,
            $endpoint,
            ['queryParams' => $queryParams]
        );

        return $response;
    }

    /**
     * This is a helper method to do a post request.
     *
     * @param       $endpoint
     * @param array $postData
     *
     * @return array
     * @throws Exceptions\ApiResponseException
     */
    public function post($endpoint, $postData = [])
    {
        $response = Http::send(
            $this,
            $endpoint,
            [
                'postFields' => $postData,
                'method'     => 'POST'
            ]
        );

        return $response;
    }

    /**
     * This is a helper method to do a put request.
     *
     * @param       $endpoint
     * @param array $putData
     *
     * @return array
     * @throws Exceptions\ApiResponseException
     */
    public function put($endpoint, $putData = [])
    {
        $response = Http::send(
            $this,
            $endpoint,
            ['postFields' => $putData, 'method' => 'PUT']
        );

        return $response;
    }

    /**
     * This is a helper method to do a delete request.
     *
     * @param $endpoint
     *
     * @return array
     * @throws Exceptions\ApiResponseException
     */
    public function delete($endpoint)
    {
        $response = Http::send(
            $this,
            $endpoint,
            ['method' => 'DELETE']
        );

        return $response;
    }
}
