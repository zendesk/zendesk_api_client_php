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
    // Find all helpcenter sections
    $sections = $client->helpCenter->sections()->findAll();
    echo "<pre>";
    print_r($sections);
    echo "</pre>";

    // Find all helpcenter sections with a specific locale
    $sections = $client->helpCenter->sections()->setLocale('en-us')->findAll();
    echo "<pre>";
    print_r($sections);
    echo "</pre>";

    // Find all helpcenter sections within a specific category
    $categoryId = 204009948;
    $sections = $client->helpCenter->categories($categoryId)->sections()->findAll();
    echo "<pre>";
    print_r($sections);
    echo "</pre>";

    // Find all helpcenter sections with a specific locale in a category
    $sections = $client->helpCenter->categories($categoryId)->sections()->setLocale('en-us')->findAll();
    echo "<pre>";
    print_r($sections);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage().'</br>';
}
