<?php

namespace Zendesk\API;

/*
 * Dead simple autoloader:
 * spl_autoload_register(function($c){@include 'src/'.preg_replace('#\\\|_(?!.+\\\)#','/',$c).'.php';});
 */

use Zendesk\API\UtilityTraits\InstantiatorTrait;

/**
 * Client class, base level access
 * @package Zendesk\API
 *
 * @method Debug debug()
 * @method Tickets ticket()
 * @method Tickets tickets()
 * @method TicketFields ticketFields()
 * @method TicketForms ticketForms()
 * @method Twitter twitter()
 * @method Attachments attachments()
 * @method Requests requests()
 * @method Views views()
 * @method Users users()
 * @method UserFields userFields()
 * @method Groups groups()
 * @method GroupMemberships groupMemberships()
 * @method CustomRoles customRoles()
 * @method Forums forums()
 * @method Categories categories()
 * @method Topics topics()
 * @method Settings settings()
 * @method ActivityStream activityStream()
 * @method AuditLogs auditLogs()
 * @method Autocomplete autocomplete()
 * @method Automations automations()
 * @method JobStatuses jobStatuses()
 * @method Macros macros()
 * @method DynamicContent dynamicContent()
 * @method OAuthClients oauthClients()
 * @method OAuthTokens oauthTokens()
 * @method OrganizationFields organizationFields()
 * @method Organizations organizations()
 * @method SatisfactionRatings satisfactionRatings()
 * @method SharingAgreements sharingAgreements()
 * @method SuspendedTickets suspendedTickets()
 * @method Tags tags()
 * @method Targets targets()
 * @method Triggers triggers()
 * @method Voice voice()
 * @method Locales locales()
 * @method PushNotificationDevices push_notification_devices()
 */
class HttpClient
{
    use InstantiatorTrait;

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
     * @var Tickets
     */
    protected $tickets;
    /**
     * @var TicketFields
     */
    protected $ticketFields;
    /**
     * @var TicketForms
     */
    protected $ticketForms;
    /**
     * @var Twitter
     */
    protected $twitter;
    /**
     * @var Attachments
     */
    protected $attachments;
    /**
     * @var Requests
     */
    protected $requests;
    /**
     * @var Users
     */
    protected $users;
    /**
     * @var UserFields
     */
    protected $userFields;
    /**
     * @var Groups
     */
    protected $groups;
    /**
     * @var GroupMemberships
     */
    protected $groupMemberships;
    /**
     * @var CustomRoles
     */
    protected $customRoles;
    /**
     * @var Forums
     */
    protected $forums;
    /**
     * @var Categories
     */
    protected $categories;
    /**
     * @var Topics
     */
    protected $topics;
    /**
     * @var Settings
     */
    protected $settings;
    /**
     * @var ActivityStream
     */
    protected $activityStream;
    /**
     * @var AuditLogs
     */
    protected $auditLogs;
    /**
     * @var Autocomplete
     */
    protected $autocomplete;
    /**
     * @var Automations
     */
    protected $automations;
    /**
     * @var JobStatuses
     */
    protected $jobStatuses;
    /**
     * @var Macros
     */
    protected $macros;
    /**
     * @var DynamicContent
     */
    protected $dynamicContent;
    /**
     * @var OAuthClients
     */
    protected $oauthClients;
    /**
     * @var OAuthTokens
     */
    protected $oauthTokens;
    /**
     * @var OrganizationFields
     */
    protected $organizationFields;
    /**
     * @var Organizations
     */
    protected $organizations;
    /**
     * @var SatisfactionRatings
     */
    protected $satisfactionRatings;
    /**
     * @var Search
     */
    protected $search;
    /**
     * @var SharingAgreements
     */
    protected $sharingAgreements;
    /**
     * @var SuspendedTickets
     */
    protected $suspendedTickets;
    /**
     * @var Tags
     */
    protected $tags;
    /**
     * @var Targets
     */
    protected $targets;
    /**
     * @var Triggers
     */
    protected $triggers;
    /**
     * @var Voice
     */
    protected $voice;
    /**
     * @var Locales
     */
    protected $locales;
    /**
     * @var PushNotificationDevices
     */
    protected $push_notification_devices;
    /**
     * @var Debug
     */
    protected $debug;
    /**
     * @var Guzzle
     */
    public $guzzle;

    /**
     * @param string $subdomain
     * @param string $username
     */

    public function __construct($subdomain, $username, $scheme = "https", $hostname = "zendesk.com", $port = 443, $guzzle = null)
    {
        if (is_null($guzzle)) {
            $this->guzzle = new \GuzzleHttp\Client();
        } else {
            $this->guzzle = $guzzle;
        }

        $this->subdomain = $subdomain;
        $this->username = $username;
        $this->hostname = $hostname;
        $this->scheme = $scheme;
        $this->port = $port;

        if (empty($subdomain)) {
            $this->apiUrl = "$scheme://$hostname:$port/api/{$this->apiVer}/";
        } else {
            $this->apiUrl = "$scheme://$subdomain.$hostname:$port/api/{$this->apiVer}/";
        }

        $this->debug = new Debug();
        $this->tickets = new Resources\Tickets($this);
        $this->views = new Resources\Views($this);
        $this->users = new Resources\Users($this);
        $this->ticketFields = new Resources\TicketFields($this);
    }

    /**
     * Configure the authorization method
     *
     * @param string $method
     * @param string $value
     */
    public function setAuth($method, $value)
    {
        switch ($method) {
            case 'password':
                $this->password = $value;
                $this->token = '';
                $this->oAuthToken = '';
                break;
            case 'token':
                $this->password = '';
                $this->token = $value;
                $this->oAuthToken = '';
                break;
            case 'oauth_token':
                $this->password = '';
                $this->token = '';
                $this->oAuthToken = $value;
                break;
        }
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
     * Returns a text value indicating the type of authorization configured
     *
     * @return string
     */
    public function getAuthType()
    {
        return ($this->oAuthToken ? 'oauth_token' : ($this->token ? 'token' : 'password'));
    }

    /**
     * Compiles an auth string with either token, password or OAuth credentials
     *
     * @return string
     */
    public function getAuthText()
    {
        return ($this->oAuthToken ? $this->oAuthToken : $this->username . ($this->token ? '/token:' . $this->token : ':' . $this->password));
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
        $this->debug->lastRequestHeaders = $lastRequestHeaders;
        $this->debug->lastResponseCode = $lastResponseCode;
        $this->debug->lastResponseHeaders = $lastResponseHeaders;
        $this->debug->lastResponseError = $lastResponseError;
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
        return ((isset($params['sideload'])) && (is_array($params['sideload'])) ? $params['sideload'] : $this->sideload);
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
     *
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
     *
     * @return mixed
     */
    public function anonymousSearch(array $params)
    {
        return $this->search->anonymousSearch($params);
    }

}
