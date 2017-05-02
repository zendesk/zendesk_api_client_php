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
    // Search the current customer
    $params = ['query' =>'customer@example.com'];
    $search = $client->users()->search($params);

    if (empty($search->users)) {
        echo "This email address could not be found on Zendesk";
    } else {
        foreach ($search->users as $userData) {
            $userId = $userData->id;
            $tickets = $client->users($userId)->requests()->findAll();

            // Show the results
            echo "<pre>";
            print_r($tickets);
            echo "</pre>";
        }
    }
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo $e->getMessage().'</br>';
}
