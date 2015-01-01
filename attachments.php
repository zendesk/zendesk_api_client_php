<h4>Attachments</h4>
<form action="attachments.php" method="POST" enctype="multipart/form-data">
	<select name="attachment_form">
		<option value="getAttachment">Get Attachment by ID</option>
		<option value="deleteAttachment">Delete Attachment by ID</option>
		<option value="uploadAttachment">Upload Attachment</option>
		<option value="redactAttachment">Redact Attachment</option>
	</select></br>
	<input type="checkbox" name="attachment_token" value="token"/>Upload Token </br>
	<input type="text" name="attachment_id" placeholder="attachment ID"/></br>
	<input type="text" name="ticket_id" placeholder="ticket ID"/></br>
	<input type="text" name="comment_id" placeholder="commet ID"/></br>
	<input type="file" name="file"/></br>
	<button type="submit">Submit</button></br>
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

$subdomain = "";
$username = "";
$token = ""; // replace this with your token

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password


$attachment = array();
$attachments = $client->attachments();

function print_me($attachment) {
	echo "<pre>";
	print_r($attachment);
	echo "</pre>";
}

function create_upload($attachments) {
	return $attachments->upload(array('file' => './tests/assets/UK.png'));
}

switch ($_REQUEST['attachment_form']){
// Get attachment by ID	
	case "getAttachment":
		$attachment['id'] = (!empty($_REQUEST['attachment_id'])) ? $_REQUEST['attachment_id'] : create_upload($attachments)->upload->attachment->id;
		print_me($attachments->find($attachment));
		break;
	case "deleteAttachment":
// Delete attachment by token or id
		
		if (!empty($_REQUEST['attachment_id'])) { 
			(($_REQUEST['attachment_token'] == "token")) ? $attachment['token'] = $_REQUEST['attachment_id'] : $attachment['id'] = $_REQUEST['attachment_id'];
			print_me($attachment);
			if ($attachments->delete($attachment)) echo "The attachment was deleted successfully.";
			else echo "There was an error deleting the attachment.  Please try again.";
		} else {
			if ($attachments->delete(array("id" => create_upload($attachments)->upload->attachment->id))) "The attachment was deleted successfully.";
			else echo "There was an error deleting the attachment.  Please try again.";
		}
		break;
	case "uploadAttachment":
	// Upload an attachment
		if (!empty($_FILES["file"]["tmp_name"])){
			$attachment['file'] = $_FILES["file"]["tmp_name"];
			print_me($attachments->upload($attachment));
		} else echo "please add a file if you want to upload";
		break;
	case "redactAttachment":
	// Delete an attachment from within a ticket (redact)
		if (!empty($_REQUEST['comment_id']) && !empty($_REQUEST['ticket_id']) && !empty($_REQUEST['attachment_id'])){
			$attachment['ticket'] = $_REQUEST['ticket_id'];
			$attachment['comment'] = $_REQUEST['comment_id'];
			$attachment['attachment'] = $_REQUEST['attachment_id'];
			if($attachments->redact($attachment) === FALSE) echo "The attachment was successfully redacted";
			else echo "There was a failure with the redaction, please try again";
		} else {
			echo "Please enter a ticket id, a comment id, and an attachment id";
		}

		break;
}




?>
