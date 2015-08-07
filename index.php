<?php
include("vendor/autoload.php");

use Zendesk\API\HttpClient as ZendeskAPI;

$subdomain = "subdomain";
$username  = "email@company.com";
$token     = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv"; // replace this with your token

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('basic', ['username' => $username, 'token' => $token]);

// Get all tickets
$tickets = $client->tickets()->findAll();
print_r($tickets);

// Create a new ticket
$newTicket = $client->tickets()->create(array(
  'subject'  => 'The quick brown fox jumps over the lazy dog',
  'comment'  => array(
    'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
  ),
  'priority' => 'normal'
));
print_r($newTicket);
