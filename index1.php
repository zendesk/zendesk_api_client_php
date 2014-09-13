<?php
include("vendor/autoload.php");

use Zendesk\API\Client as ZendeskAPI;

$subdomain = "z3nburmaglot";
$username = "japeterson@zendesk.com";
$token = "gJ8wPpHhEDE9q632sZO32yVBAOkYLaELM7ZOFFQE"; // replace this with your token
//$password = "123456";

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password





?>