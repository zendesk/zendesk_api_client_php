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
    // Create a new HelpCenter Article
    $sectionId = 1;
    $article = $client->helpCenter->sections($sectionId)->articles()->create([
        'locale' => 'en-us',
        'title' => 'Smartest Fish in the World',
    ]);
    echo "<pre>";
    print_r($article);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage().'</br>';
}

