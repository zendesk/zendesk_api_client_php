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
  // Delete a ticket by id
  $id = '51';
  $deleteTicket = $client->tickets()->delete($id);
  echo "Ticket ($id) has been removed";
}
 catch (\Zendesk\API\Exceptions\ApiResponseException $e)
 {
   echo 'Please check your credentials. Make sure to change the $subdomain, $username, and $token variables in this file.';
 }
