<?php
include("../../vendor/autoload.php");

use Zendesk\API\HttpClient as ZendeskAPI;

/**
 * Replace the following with your own.
 */

 $subdomain = "idevelopment";
 $username  = "glenn.hermans@idevelopment.be";
 $token     = "SrPgNIFHvzT9u9UxevHq6wB9M0S8lkDxuVWc52np";

 $client = new ZendeskAPI($subdomain);
 $client->setAuth('basic', ['username' => $username, 'token' => $token]);

 try {
   $query = $client->groups()->findAll();
   foreach($query as $UserData)
   {
     echo "<pre>";
     print_r($UserData);
     echo "</pre>";
   }
 }

 catch (\Zendesk\API\Exceptions\ApiResponseException $e)
 {
   echo 'Please check your credentials. Make sure to change the $subdomain, $username, and $token variables in this file.';
 }
