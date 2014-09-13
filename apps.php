<h4>Apps</h4>
<form action="apps.php" method="POST" enctype="multipart/form-data">
	<select name="apps_form">
		<option value="uploadApp">Upload App</option>
		<option value="createApp">Create App</option>
		<option value="getJobStatus">Get Job Status</option>
		<option value="installApp">Install an App</option>
		<option value="getInstallations">Get all App Installations</option>
		<option value="getInstallation">Get an App Installation</option>
	</select>
	<input type="text" placeholder="App ID" name="app_id"></input>
	<input type="text" placeholder="Settings - Note that this must be a Hash of all settings required in Manifest.json" name="settings"></input>
	<input type="text" placeholder="Job ID" name="jobID"></input>
	<input type="text" placeholder="App Name" name="appName"></input>
	<input type="text" placeholder="App Description" name="appDescription"></input>
	<input type="text" placeholder="App Upload ID" name="upload_id"></input>
	<input type="file" name="file"/>
	<button type="submit">Submit</button>
</form>

<?php
include("vendor/autoload.php");

//anything with file upload will not dynamically generate content.  Oauth tokens will not dynamically generate content.  Users will dynamically generate content.  Apps will not dynamically generate content.  
// made the upload work.  This will be the last endpoint to be finished.

use Zendesk\API\Client as ZendeskAPI;

$subdomain = "z3nburmaglot";
$username = "japeterson@zendesk.com";
$token = "FsD6L6pHGSsoHFctgl0HsPVATjEepNRHEwr2zycl"; // replace this with your token


$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password

$apps = array();
$mapps = $client->apps();

function print_me($apps) {
	echo "<pre>";
	print_r($apps);
	echo "</pre>";
}

switch ($_REQUEST['apps_form']) {
	case "uploadApp":
		$apps['file'] = $_FILES['file']['tmp_name'];
		print_me($mapps()->upload($apps));
		break;
	case "createApp":
		$apps['name'] = $_REQUEST['appName'];
		$apps['short_description'] = $_REQUEST['appDescription'];
		$apps['upload_id'] = $_REQUEST['upload_id'];
		print_me($mapps()->create($apps));
		break;
	case "getJobStatus":
		$apps['id'] = $_REQUEST['jobID'];
		print_me($mapps()->getJob($apps));
		break;
	case "installApp":
		$apps['app_id'] = $_REQUEST['app_id'];
		$apps['settings'] = array(
			"name" => "App Via Wrapper",
			"key" => "some text",
			"subdomain" => "z3nburmaglot",
			"new_data" => "some data"
		);
		print_me($mapps()->install($apps));
		break;
	case "getInstallations":
		print_me($mapps()->getInstallations());
		break;
	case "getInstallation":
		$apps['id'] = $_REQUEST['app_id'];
		print_me($mapps()->getInstallation($apps));
}

?>