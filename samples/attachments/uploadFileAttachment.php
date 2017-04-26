<?php

include("../../vendor/autoload.php");

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
    // Upload an existing file attachment.
    $attachment = $client->attachments()->upload(array(
        'file' => '../../tests/assets/UK.png',
        'type' => 'image/png',
        'name' => 'UK test non-alpha chars.png'
    ));

    // Show result
    echo "<pre>";
    print_r($attachment);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage().'</br>';
}
