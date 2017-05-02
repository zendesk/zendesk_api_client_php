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

try {
    // Update a new ticket
    $updateTicket = $client->tickets()->update(1, [
        'priority' => 'urgent',
        'comment'  => [
            'body' => 'We have changed your ticket priority to Urgent and will keep you up-to-date asap.'
        ],
    ]);

    // Show result
    echo "<pre>";
    print_r($updateTicket);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage().'</br>';
}
