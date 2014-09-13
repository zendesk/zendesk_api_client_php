<h4>OAuth Tokens</h4>
<form action="oauthtokens.php" method="POST">
	<select name="formBox">
		<option value="getAllTokens">Get All OAuth Tokens</option>
		<option value="getToken">Get an OAuth Token</option>
		<option value="deleteToken">Delete an OAuth Token</option>
		<option value="createToken">Create an OAuth Token</option>
	</select>
	<input type="text" placeholder="OAuth Token ID" name="tokenID"></input>
	<button type="submit">Submit</button>
</form>
<?php

//Need to set this up to generate a new OAuth token for destruction
include("vendor/autoload.php");

use Zendesk\API\Client as ZendeskAPI;

$subdomain = "z3nburmaglot";
$username = "japeterson@zendesk.com";
$token = "FsD6L6pHGSsoHFctgl0HsPVATjEepNRHEwr2zycl"; // replace this with your token

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password

$tokenID = $_REQUEST['tokenID'];

function print_me($value) {
	echo "<pre>";
	print_r($value);
	echo "</pre>";
}
$oauth = $client->oauthTokens();
$oauth_tokens = $oauth->findAll()->tokens;

switch ($_REQUEST['formBox']) {
	case "getAllTokens":
		print_me($oauth_tokens);
		break;
	case "getToken":
		print_me($oauth->find(array("id" => (!empty($tokenID)) ? $tokenID : $oauth_tokens[array_rand($oauth_tokens)]->id)));
		break;
	case "deleteToken":
		print_me($oauth->revoke((array("id" => (!empty($tokenID)) ? $tokenID : 118496))));
		break;
	case "createToken":
	
		break;
}



?>