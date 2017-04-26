<?php

include("../../vendor/autoload.php");

use GuzzleHttp\Psr7\LazyOpenStream;
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
    // Upload a dynamically created file stream.
    $attachment = $client->attachments()->upload(array(
        'file' => new LazyOpenStream('../../tests/assets/UK.png', 'r'),
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
