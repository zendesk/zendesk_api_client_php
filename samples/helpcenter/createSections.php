<?php

include('../../vendor/autoload.php');

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
    // Create a new HelpCenter Section
    $categoryId = 1;
    $section = $client->helpCenter->categories($categoryId)->sections()->create([
        'position' => 1,
        'locale' => 'en-us',
        'name' => 'Super Hero Tricks',
        'description' => 'This section contains a collection of super hero tricks',
    ]);
    echo "<pre>";
    print_r($section);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage().'</br>';
}
