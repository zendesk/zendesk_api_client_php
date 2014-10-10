<h4>Attachments</h4>
<form action="attachments.php" method="POST" enctype="multipart/form-data">
	<select name="attachment_form">
		<option value="getAttachment">Get Attachment by ID</option>
		<option value="deleteAttachment">Delete Attachment by ID</option>
		<option value="uploadAttachment">Upload Attachment</option>
		<option value="redactAttachment">Redact Attachment</option>
	</select>
	<input type="radio" name="token_or_id" value="attachment_token"/>Attachment Token </br>
	<input type="radio" name="token_or_id" value="attachment_id"/>Attachment ID
	<input type="text" name="attachment_id" placeholder="attachment ID"/>
	<input type="file" name="file"/>
	<button type="submit">Submit</button>
</form>
<?php

// Need to make slight modification in Attachments.php for uploadAttachment to work if using >= PHP 5.5.0 
// need to add endpoint for redacting attachments.

// No dynamically generated content for file uploads/deletions/redactions/gets)
// Attachment viewer app?

// Attachment App?
// Get all Attachments?

include("vendor/autoload.php");

use Zendesk\API\Client as ZendeskAPI;

$subdomain = "z3nburmaglot";
$username = "japeterson@zendesk.com";
$token = "FsD6L6pHGSsoHFctgl0HsPVATjEepNRHEwr2zycl"; // replace this with your token

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password


$attachment = array();
copy('http://burmaglot.com/static/images/headerlogo.png', '/tmp/sample.jpeg');
print_r($attachment_image);
$attachments = $client->attachments();

function print_me($attachment) {
	echo "<pre>";
	print_r($attachment);
	echo "</pre>";
}
switch ($_REQUEST['attachment_form']){
// Get attachment by ID	
// Need to make dynamically generated id if no user input
	case "getAttachment":
		print_me($attachments->find(array("id" => (!empty($_REQUEST['attachment_id'])) ? $_REQUEST['attachment_id'] : 755057426)));
		break;
	case "deleteAttachment":
// Delete attachment by token or id
		if ($_REQUEST['token_or_id'] == "attachment_token")	print_me($attachments->delete(array("token"=> $_REQUEST['attachment_id'])));
		elseif ($_REQUEST['token_or_id'] == "attachment_id") print_me($attachments->delete(array("id" => $_REQUEST['attachment_id'])));
		//$attachment['token'] = $_REQUEST['upload_token']'ixsNPS3z1CZqlWs69QQIw88Co';	
		break;
	case "uploadAttachment":
	// Upload an attachment
		if (!empty($_FILES["file"]["tmp_name"])){
			print_r($_FILES);
			$attachment['file'] = $_FILES["file"]["tmp_name"];
			$attachment['name'] = $_FILES["file"]["name"];
			$attachment['type'] = $_FILES["file"]["type"];
			//$attachment['file'] = $attachment_image;
			print_me($attachments->upload($attachment));
		}else echo "please add a file if you want to upload";

		break;
	case "redactAttachment":
	// Delete an attachment from within a ticket (redact)
	//  Added this and the logic to process redaction
	// Need to allow user to input this information
		$attachment['ticket'] = 120;
		$attachment['comment'] = 30630728378;
		$attachment['attachment'] = 774131528;
		print_me($attachments->redact($attachment));
		break;
}


?>