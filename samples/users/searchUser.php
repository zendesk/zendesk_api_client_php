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
    // Search the current customer
    $params = array('query' => 'demo@example.com');
    $search = $client->users()->search($params);
    // verify if this email address exists
    if (empty($search->users)) {
        echo 'This email adress could not be found on Zendesk.';
    } else {
        foreach ($search->users as $UserData) {
            echo "<pre>";
            print_r($UserData);
            echo "</pre>";
        }
    }
} catch (\Zendesk\API\Exceptions\ApiResponseException $e) {
    echo 'Please check your credentials. Make sure to change the $subdomain, $username, and $token variables in this file.';
}
