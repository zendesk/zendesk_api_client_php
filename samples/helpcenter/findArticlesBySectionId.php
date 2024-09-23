<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Zendesk\API\HttpClient as ZendeskAPI;

/**
 * Replace the following with your own.
 */

$subdomain = "subdomain";
$username = "email@example.com";
$token = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv";
$section_id = 10801195364239; // replace this with your section id

$client = new ZendeskAPI($subdomain);
$client->setAuth('basic', ['username' => $username, 'token' => $token]);
$help_center_client = new Zendesk\API\Resources\HelpCenter($client);

try {
    // Find all helpcenter category with the given section id
    $articles = $help_center_client->sections($section_id)->articles()->findAll();

    echo "<pre>";
    print_r($articles);
    echo "</pre>";
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage().'</br>';
}
