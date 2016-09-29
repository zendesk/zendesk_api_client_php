<?php
include("../vendor/autoload.php");

use Zendesk\API\HttpClient as ZendeskAPI;

/**
 * Replace the following with your own.
 */
$subdomain = "subdomain";
$username  = "email@example.com";
$token     = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv";

$client = new ZendeskAPI($subdomain);
$client->setAuth('basic', ['username' => $username, 'token' => $token]);

try {

    $user_id = 55555555555; // requester_id

    $tickets = $client->tickets()->findByUser($user_id, [
        'per_page' => 10,
        'page' => 1,
        'sort_by' => 'created_at',
        'sort_order' => 'desc'
    ]);

    print_r($tickets);

} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo 'Records not found.';
}

