<h4>Satisfaction Ratings</h4>
<form action="satisfaction.php" method="POST">
	<select name="formBox">
		<option value="getAllRatings">Get All Satisfaction Ratings</option>
		<option value="getRatingById">Get a Satisfaction Rating By Id</option>
		<option value="createRating">Create a new Rating</option>
	</select>
	<input type="number" name="satisfaction_id" placeholder="enter a satisfaction rating ID here"></input>
	<input type="number" name="ticket_id" placeholder="enter a ticket ID here"></input>
	<input type="text" name="score" placeholder="enter a ticket score here (good/bad)"></input>
	<input type="comment" name="comment" placeholder="enter a ticket comment here"></input>
	<button type="submit">Submit</button>
</form>

<?php
// Need to add "protected $satisfactionRatings;" to "class Client" in Client.php,
// and "$this->satisfactionRatings = new SatisfactionRatings($this);" to public funtion __construct($subdomain, $username) in Client.php

//Everything works.  Just need to allow user to add his own data.

include("vendor/autoload.php");
use Zendesk\API\Client as ZendeskAPI;
$value = $_REQUEST['formBox'];

$subdomain = "z3nburmaglot";
$username = "japeterson@zendesk.com";
$end_user = "jpete369@gmail.com";
$token = "FsD6L6pHGSsoHFctgl0HsPVATjEepNRHEwr2zycl";

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password
$end_client = new ZendeskAPI($subdomain, $end_user);
$end_client->setAuth('token', $token);

$satisfaction = $client->satisfactionRatings();
$ratings = $satisfaction->findall()->satisfaction_ratings;

$rating = array();
function print_me($rating) {
	echo "<pre>";
	print_r($rating);
	echo "</pre>";
}

switch ($value) {
	case "getAllRatings": 
	// Get all satisfaction ratings
		print_me($ratings);
		break;
	case "getRatingById":
	// Get a satisfaction rating by its ID  - allow for user input, if there is none then grab a random rating from the first 100 ratings
		if ($_POST['satisfaction_id'] != null) print_me($satisfaction->find(array("id"=>$_POST['satisfaction_id']))->satisfaction_rating);
		else print_me($ratings[array_rand($ratings)]);
		break;
	case "createRating":
	// Create a satisfaction rating for a ticket  -  allow for user input (of ticket ID that belongs to $end_user provided above, if there is none then create a new ticket and add a comment.
		$array = array("subject" => "general inquiries", 
					"comment" => array("body" => "Here are the details"), 
					"requester" => array("email" =>$end_user), 
					"status" => "solved");
		($_POST['comment'] != null) ? $rating['comment'] = $_POST['comment'] : $rating['comment'] = "Added via PHP API Wrapper at ".time();
		($_POST['score'] != null) ? $rating['score'] = $_POST['score'] : $rating['score'] = "good";
		($_POST['ticket_id'] != null) ? ($rating['ticket_id'] = $_POST['ticket_id']) : $rating['ticket_id'] = $client->tickets()->create($array)->ticket->id;
		print_me($end_client->satisfactionRatings()->create($rating));
		break;
}
?>

