<?php
include("vendor/autoload.php");

use Zendesk\API\Client as ZendeskAPI;

$subdomain = "z3nburmaglot";
$username = "japeterson@zendesk.com";
$token = "FsD6L6pHGSsoHFctgl0HsPVATjEepNRHEwr2zycl"; // replace this with your token
//$password = "HjV19nQm";

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password
//$client->setAuth('password', $password);
//statuses.json method seems to be missing

$ticketsInfo = array(
	"subject" => "general_inquiries",
	"comment" => array(
		"body" => "Here are the details"
	),
	"requester" => array(
		"name" => "Test",
		"email" => "test@user.com"
	),	
	"priority" => "normal",
	"tags" => array("contactform"),
	"custom_fields" => array(
		array( "id" => 23906213, "value" => "vai bem"),
		array( "id" => 23906223, "value" => "va bien")
	)
);



	$tickets = $client->tickets()->create($ticketsInfo);
	echo "<pre>";
	print_r($tickets);
	echo "</pre>";





?>