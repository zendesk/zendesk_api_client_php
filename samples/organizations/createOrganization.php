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
    $newOrganization = $client->organizations()->create(
        [
        'external_id' => '123567854',
        'name' => 'Example_Organization',
        'shared_tickets' => 1,
        'shared_comments' => 1,
        'tags' => array('dog', 'cat', 'fish', 'strange_creature'),
        'organization_fields' => array('favorite animal' => 'fish')
        ]
    );
    // Show result
    echo "<pre>";
    print_r($newOrganization);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo 'Please check your credentials. Make sure to change the $subdomain, $username, and $token variables in this file.';
}
