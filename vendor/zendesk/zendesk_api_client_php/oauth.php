<?php
include("vendor/autoload.php");

use Zendesk\API\Client as ZendeskAPI;
use Zendesk\API\Http;

$subdomain = "z3nburmaglot";       // Your Zendesk subdomain
$username = "japeterson@zendesk.com";         // Your Zendesk login
$oAuthId = "myo";          // The value you entered into the OAuth 'Unique Identifier' field
$oAuthSecret = "e661791f1e22aa9bae0ee4745828f1d35078fd3943da2d54647cc7ab33ea8e58";	// The OAuth secret given to you by Zendesk



$client = new ZendeskAPI($subdomain, $username);
if ($_REQUEST['code']) {
	$response = Http::oauth($client, $_REQUEST['code'], $oAuthId, $oAuthSecret);
	if (($client->getDebug()->lastResponseCode == 200) && ($response->access_token)) {
		echo "<h1>Success!</h1>";
		echo "<p>Your OAuth token is: ".$response->access_token."</p>";
		echo "<p>Use this code before any other API call:</p>";
		echo "<code>&lt;?<br />\$client = new ZendeskAPI(\$subdomain, \$username);<br />\$client->setAuth('oauth_token', '".$response->access_token."');<br />?&gt;</code>";
	} else {
		echo "<h1>Error!</h1>";
		echo "<p>We couldn't get an access token for you. Please check your credentials and try again.</p>";
	}
} else {
	echo "<a href=\"https://".$subdomain.".zendesk.com/oauth/authorizations/new?response_type=code&redirect_uri=".($_SERVER['HTTPS'] ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."&client_id=".$oAuthId."&scope=read%20write\">Click to request an OAuth token</a>";
}
?>
