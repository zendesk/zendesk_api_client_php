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
    // Create a new ticket
    $newTicket = $client->tickets()->create([
        'type' => 'problem',
        'tags'  => array('demo', 'testing', 'api', 'zendesk'),
        'subject'  => 'The quick brown fox jumps over the lazy dog',
        'comment'  => array(
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
        ),
        'requester' => array(
            'locale_id' => '1',
            'name' => 'Example User',
            'email' => 'customer@example.com',
        ),
        'priority' => 'normal',
    ]);

    // Show result
    echo "<pre>";
    print_r($newTicket);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage().'</br>';
}
