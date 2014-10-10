<h4>Locales</h4>
<form action="locales.php" method="POST">
	<select name="locales_form">
		<option value="getAllLocales">Get All Locales</option>
		<option value="getAgentLocale">Get Locales Localized for Agent</option>
		<option value="localeById">Get Locale by ID</option>
		<option value="detectBest">Detect Best Locale for User</option>
		<option value="currentLocale">Get Locale of Current User</option>
	</select>
	<input type="text" placeholder="Enter Locale ID or bcp-47 code" name="locale_id"></input>
	<button type="submit">Submit</button>
</form>

<?php
include("vendor/autoload.php");

use Zendesk\API\Client as ZendeskAPI;

$subdomain = "z3nburmaglot";
$username = "japeterson@zendesk.com";
$token = "FsD6L6pHGSsoHFctgl0HsPVATjEepNRHEwr2zycl"; 

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password

$locales = $client->locales();
$all_locales = $locales->findall()->locales;

function print_me($locales) {
	echo "<pre>";
	print_r($locales);
	echo "</pre>";
}

switch ($_REQUEST['locales_form']) {
	case "getAllLocales":
	// Get all Locales that are available for account
		print_me($locales->findall());
		break;
	case "getAgentLocale":
	// Get all Locales that have been localized for agent
		print_me($locales->findall(array("agent"=>true)));
		break;
	case "localeById":
	// Get locale by ID or by bcp-47 code		
		print_me($locales->find(array("id"=>(!empty($_REQUEST['locale_id'])) ? $_REQUEST['locale_id'] : $all_locales[mt_rand(0, count($all_locales))]->id)));
		break;
	case "detectBest":
	// Detect best language for user from the supplied list
		print_me($locales->detectBest(array("available_locales" => $all_locales)));
		break;
	case "currentLocale":
	// find locale of current user (the user making the API request?)
	// looks like two methods in wrapper perform this (findall()); and (currentLocale);
		print_me($locales->findall(array('current'=>true)));
		break;
}

?>