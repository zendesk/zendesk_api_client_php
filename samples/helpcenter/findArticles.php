<?php

include("../../vendor/autoload.php");

use Zendesk\API\HttpClient as ZendeskAPI;

/**
 * Replace the following with your own.
 */

$subdomain = "subdomain";
$username = "email@example.com";
$token = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv";

$client = new ZendeskAPI($subdomain);
$client->setAuth('basic', ['username' => $username, 'token' => $token]);
$help_center_client = new Zendesk\API\Resources\HelpCenter($client);

try {
    // Find all helpcenter categories
    $articles = $help_center_client->articles()->findAll();

    echo "<pre>";
    print_r($articles);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage() . '</br>';
}
