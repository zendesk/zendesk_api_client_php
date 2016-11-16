<?php
include("../../vendor/autoload.php");

use Zendesk\API\HttpClient as ZendeskAPI;

/**
 * Replace the following with your own.
 */

$subdomain  = "teasetoremember";
$username   = "teasetoremember@mailinator.com";
$token      = "MJLzg4N0pwL92a6D0u5IB9Mka5PX9OL4lLC5cGtl";
$attachment = '/Volumes/workspace/zendesk_api_client_user/sample.jpg';

$client = new ZendeskAPI($subdomain);
$client->setAuth('basic', ['username' => $username, 'token' => $token]);

try {
    // Upload file
    $attachment = $client->attachments()->upload([
        'file' => $attachment,
        'type' => 'image/jpg',
        'name' => 'sample.jpg'
    ]);

    // Create a new ticket with attachment
    $newTicket = $client->tickets()->create(array(
        'type' => 'problem',
        'tags'  => array('demo', 'testing', 'api', 'zendesk'),
        'subject'  => 'The quick brown fox jumps over the lazy dog',
        'comment'  => array(
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'uploads' => [$attachment->upload->token]
        ),
        'requester' => array(
            'locale_id' => '1',
            'name' => 'Example User',
            'email' => 'customer@example.com',
        ),
        'priority' => 'normal',
    ));

    // Show result
    echo "<pre>";
    print_r($newTicket);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo 'Please check your credentials. Make sure to change the $subdomain, $username, and $token variables in this file.';
}
