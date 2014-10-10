<h4>OAuth Clients</h4>
<form action="oauthclients.php" method="POST">
	<select name="oauth_form">
		<option value="getAllClients">Get All Oauth Clients</option>
		<option value="getClient">Get an Oauth Client by ID</option>
		<option value="createClient">Create an Oauth Client</option>
		<option value="updateClient">Update an Oauth Client</option>
		<option value="deleteClient">Delete an Oauth Client</option>
	</select>
	<input type="text" placeholder="Oauth Client ID" name="clientID"></input>
	<input type="text" placeholder="Oauth Client Name" name="client_name"></input>
	<input type="text" placeholder="Oauth Client Identifier" name="client_identifier"></input>
	<input type="text" placeholder="Oauth Client Redirect URI" name="client_redirect_uri"></input>
	<button type="submit">Submit</button>
</form>

<?php
include("vendor/autoload.php");

use Zendesk\API\Client as ZendeskAPI;

$subdomain = "z3nburmaglot";
$username = "japeterson@zendesk.com";
$token = "FsD6L6pHGSsoHFctgl0HsPVATjEepNRHEwr2zycl"; // replace this with your token

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password

$oclient_id = $_REQUEST['clientID'];


$clientID = Array();

$r_number = mt_rand(5, 150000);
$clientID['name'] = (!empty($_REQUEST["client_name"])) ? $_REQUEST["client_name"] : 'Test from API Wrapper #'.$r_number;
$clientID['identifier'] = (!empty($_REQUEST["client_identifier"])) ? $_REQUEST["client_identifier"] :  "Some identifier #".$r_number;
//This needs to be a string separated by newline characters rather than an array
$clientID['redirect_uri'] = (!empty($_REQUEST["client_redirect_uri"])) ? $_REQUEST["client_redirect_uri"] : "http://localhost/PIP/main.php\nhttp://localhost/PIP/main.php/oauth";


$oauth = $client->oauthClients();
$oauth_clients = $oauth->findAll()->clients;

function print_me($value) {
	echo "<pre>";
	print_r($value);
	echo "</pre>";
}

switch ($_REQUEST['oauth_form']) {
	case "getAllClients":
		print_me($oauth_clients);
		break;
	case "getClient":
		print_me($oauth->find(array("id" => (!empty($oclient_id)) ? $oclient_id : $oauth_clients[array_rand($oauth_clients)]->id)));
		break;
	case "createClient":
		print_me($oauth->create($clientID));
		break;
	case "updateClient":
		$clientID['id'] = 1843;
		print_me($oauth->update($clientID));		
		break;
	case "deleteClient":
		$fodder = $oauth->create($clientID)->client->id;
		print_me((($oauth->delete(array("id"=>!empty($oclient_id) ? $oclient_id : $fodder)))==1) ? "client #".$fodder." deleted successfully" : "there was an error");
		break;
}

?>