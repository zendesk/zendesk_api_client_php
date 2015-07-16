<?php

namespace Zendesk\API;

/*
 * Dead simple autoloader:
 * spl_autoload_register(function($c){@include 'src/'.preg_replace('#\\\|_(?!.+\\\)#','/',$c).'.php';});
 */

use Zendesk\API\Exceptions\AuthException;
use Zendesk\API\Resources\Activities;
use Zendesk\API\Resources\AppInstallations;
use Zendesk\API\Resources\Attachments;
use Zendesk\API\Resources\AuditLogs;
use Zendesk\API\Resources\Autocomplete;
use Zendesk\API\Resources\Automations;
use Zendesk\API\Resources\Brands;
use Zendesk\API\Resources\CustomRoles;
use Zendesk\API\Resources\DynamicContent;
use Zendesk\API\Resources\GroupMemberships;
use Zendesk\API\Resources\Groups;
use Zendesk\API\Resources\Incremental;
use Zendesk\API\Resources\JobStatuses;
use Zendesk\API\Resources\Locales;
use Zendesk\API\Resources\Macros;
use Zendesk\API\Resources\OAuthClients;
use Zendesk\API\Resources\OAuthTokens;
use Zendesk\API\Resources\OrganizationFields;
use Zendesk\API\Resources\OrganizationMemberships;
use Zendesk\API\Resources\Organizations;
use Zendesk\API\Resources\OrganizationSubscriptions;
use Zendesk\API\Resources\Requests;
use Zendesk\API\Resources\SatisfactionRatings;
use Zendesk\API\Resources\Search;
use Zendesk\API\Resources\SharingAgreements;
use Zendesk\API\Resources\Tags;
use Zendesk\API\Resources\Targets;
use Zendesk\API\Resources\TicketImports;
use Zendesk\API\Resources\Tickets;
use Zendesk\API\Resources\Triggers;
use Zendesk\API\Resources\UserFields;
use Zendesk\API\Resources\Users;
use Zendesk\API\Resources\Views;
use Zendesk\API\Traits\Utility\InstantiatorTrait;
use Zendesk\API\Utilities\Auth;

/**
 * Client class, base level access
 *
 * @method Activities activities()
 * @method Attachments attachments()
 * @method AuditLogs auditLogs()
 * @method Automations automations()
 * @method Debug debug()
 * @method DynamicContent dynamicContent()
 * @method Groups groups()
 * @method Incremental incremental()
 * @method Locales locales()
 * @method Macros macros()
 * @method OAuthClients oauthClients()
 * @method OrganizationFields organizationFields()
 * @method Organizations organizations()
 * @method Requests requests()
 * @method Search search()
 * @method SatisfactionRatings satisfactionRatings()
 * @method SharingAgreements sharingAgreements()
 * @method Tags tags()
 * @method Targets targets()
 * @method Tickets ticket()
 * @method TicketImports ticketImports()
 * @method Triggers triggers()
 * @method UserFields userFields()
 * @method Users users()
 * @method Views views()
 *
 */
class HttpClient
{
    use InstantiatorTrait;

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

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getValidSubResources()
    {
        return [
            'activities'                => Activities::class,
            'appInstallations'          => AppInstallations::class,
            'attachments'               => Attachments::class,
            'auditLogs'                 => AuditLogs::class,
            'autocomplete'              => Autocomplete::class,
            'automations'               => Automations::class,
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
            'requests'                  => Requests::class,
            'satisfactionRatings'       => SatisfactionRatings::class,
            'sharingAgreements'         => SharingAgreements::class,
            'search'                    => Search::class,
            'tags'                      => Tags::class,
            'targets'                   => Targets::class,
            'tickets'                   => Tickets::class,
            'ticketImports'             => TicketImports::class,
            'triggers'                  => Triggers::class,
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
    public function getSideload(array $params = null)
    {
        if ((isset($params['sideload'])) && (is_array($params['sideload']))) {
            return $params['sideload'];
        } else {
            return $this->sideload;
        }
    }

    public function get($endpoint, $queryParams = [])
    {
        $sideloads = $this->getSideload($queryParams);

        // TODO: filter allowed query params
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
