<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Zendesk\API\HttpClient as ZendeskAPI;

/**
 * Replace the following with your own.
 */

$subdomain = "api.futuresimple.com";
$token = "super_secret_oauth_token"; // this must be your oauth bearer token for sell

$client = new ZendeskAPI($subdomain);
$client->setAuth('oauth', ['token' => $token]);
$sell_client = new Zendesk\API\Resources\Sell($client);

try {
    // Find all sell contacts
    $contacts = $sell_client->contacts()->findAll();

    echo "<pre>";
    print_r($contacts);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage() . '</br>';
}
