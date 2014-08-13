<?php

namespace Zendesk\API;

/*
 * Dead simple autoloader:
 * spl_autoload_register(function($c){@include 'src/'.preg_replace('#\\\|_(?!.+\\\)#','/',$c).'.php';});
 */

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
 * @method OAuthClients oauthClients()
 * @method OAuthTokens oauthTokens()
 * @method OrganizationFields organizationFields()
 * @method Organizations organizations()
 * @method SharingAgreements sharingAgreements()
 * @method SuspendedTickets suspendedTickets()
 * @method Tags tags()
 * @method Targets targets()
 * @method Triggers triggers()
 * @method Voice voice()
 * @method Locales locales()
 */
class Client {

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
     * @var Debug
     */
    protected $debug;

    /**
     * @param string $subdomain
     * @param string $username
     */
    public function __construct($subdomain, $username) {
        $this->subdomain = $subdomain;
        $this->username = $username;
        $this->apiUrl = 'https://'.$subdomain.'.zendesk.com/api/'.$this->apiVer.'/';
        $this->debug = new Debug();
        $this->tickets = new Tickets($this);
        $this->ticketFields = new TicketFields($this);
        $this->ticketForms = new TicketForms($this);
        $this->twitter = new Twitter($this);
        $this->attachments = new Attachments($this);
        $this->requests = new Requests($this);
        $this->views = new Views($this);
        $this->users = new Users($this);
        $this->userFields = new UserFields($this);
        $this->groups = new Groups($this);
        $this->groupMemberships = new GroupMemberships($this);
        $this->customRoles = new CustomRoles($this);
        $this->forums = new Forums($this);
        $this->categories = new Categories($this);
        $this->topics = new Topics($this);
        $this->settings = new Settings($this);
        $this->activityStream = new ActivityStream($this);
        $this->auditLogs = new AuditLogs($this);
        $this->autocomplete = new Autocomplete($this);
        $this->automations = new Automations($this);
        $this->jobStatuses = new JobStatuses($this);
        $this->macros = new Macros($this);
        $this->oauthClients = new OAuthClients($this);
        $this->oauthTokens = new OAuthTokens($this);
        $this->organizationFields = new OrganizationFields($this);
        $this->organizations = new Organizations($this);
        $this->search = new Search($this);
        $this->sharingAgreements = new SharingAgreements($this);
        $this->suspendedTickets = new SuspendedTickets($this);
        $this->tags = new Tags($this);
        $this->targets = new Targets($this);
        $this->triggers = new Triggers($this);
        $this->voice = new Voice($this);
        $this->locales = new Locales($this);
    }

    /**
     * Configure the authorization method
     *
     * @param string $method
     * @param string $value
     */
    public function setAuth($method, $value) {
        switch($method) {
            case 'password':    $this->password = $value;
                                $this->token = '';
                                $this->oAuthToken = '';
                                break;
            case 'token':        $this->password = '';
                                $this->token = $value;
                                $this->oAuthToken = '';
                                break;
            case 'oauth_token':    $this->password = '';
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
    public function getSubdomain() {
        return $this->subdomain;
    }

    /**
     * Returns the generated api URL
     *
     * @return string
     */
    public function getApiUrl() {
        return $this->apiUrl;
    }

    /**
     * Returns a text value indicating the type of authorization configured
     *
     * @return string
     */
    public function getAuthType() {
        return ($this->oAuthToken ? 'oauth_token' : ($this->token ? 'token' : 'password'));
    }

    /**
     * Compiles an auth string with either token, password or OAuth credentials
     *
     * @return string
     */
    public function getAuthText() {
        return ($this->oAuthToken ? $this->oAuthToken : $this->username.($this->token ? '/token:'.$this->token : ':'.$this->password));
    }

    /**
     * Set debug information as an object
     *
     * @param mixed $lastRequestHeaders
     * @param mixed $lastResponseCode
     * @param string $lastResponseHeaders
     */
    public function setDebug($lastRequestHeaders, $lastResponseCode, $lastResponseHeaders) {
        $this->debug->lastRequestHeaders = $lastRequestHeaders;
        $this->debug->lastResponseCode = $lastResponseCode;
        $this->debug->lastResponseHeaders = $lastResponseHeaders;
    }

    /**
     * Returns debug information in an object
     *
     * @return Debug
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     * Sideload setter
     *
     * @param array|null $fields
     *
     * @return Client
     */
    public function setSideload(array $fields = null) {
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
    public function getSideload(array $params = null) {
        return ((isset($params['sideload'])) && (is_array($params['sideload'])) ? $params['sideload'] : $this->sideload);
    }

    /**
     * Generic method to object getter. Since all objects are protected, this method
     * exposes a getter function with the same name as the protected variable, for example
     * $client->tickets can be referenced by $client->tickets()
     *
     * @param $name
     * @param $arguments
     *
     * @throws CustomException
     */
    public function __call($name, $arguments) {
        if(isset($this->$name)) {
            return ((isset($arguments[0])) && ($arguments[0] != null) ? $this->$name->setLastId($arguments[0]) : $this->$name);
        }
        $namePlural = $name.'s'; // try pluralize
        if(isset($this->$namePlural)) {
            return $this->$namePlural->setLastId($arguments[0]);
        } else {
            throw new CustomException("No method called $name available in ".__CLASS__);
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
    public function category($id) { return $this->categories->setLastId($id); }

    /**
     * @param int|null $id
     *
     * @return ActivityStream
     */
    public function activities($id = null) { return ($id != null ? $this->activityStream()->setLastId($id) : $this->activityStream()); }

    /**
     * @param int $id
     *
     * @return ActivityStream
     */
    public function activity($id) { return $this->activityStream()->setLastId($id); }

    /**
     * @param int $id
     *
     * @return JobStatuses
     */
    public function jobStatus($id) { return $this->jobStatuses()->setLastId($id); }

    /**
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     *
     * @return mixed
     */
    public function search(array $params) { return $this->search->performSearch($params); }

    /**
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     *
     * @return mixed
     */
    public function anonymousSearch(array $params) { return $this->search->anonymousSearch($params); }

}
