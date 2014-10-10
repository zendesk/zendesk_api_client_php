<h4>Users</h4>
<form action="users.php" method="POST" enctype="multipart/form-data">
	<select name="formBox">
		<option value="getAllUsers">Get All Users</option>
		<option value="suspendUser">Suspend a User</option>
		<option value="unsuspendUser">Unsuspend a User</option>
		<option value="relatedInfo">Get Related Info about a user</option>
		<option value="byGroup">Get Users By Group ID</option>
		<option value="byOrg">Get Users By Organization IDs</option>
		<option value="userById">Get User By Id</option>
		<option value="autocomplete">Autocomplete</option>
		<option value="changePassword">Change User Password</option>
		<option value="setPassword">Set Initial User Password</option>
		<option value="me">Get information about yourself</option>
		<option value="updateProfileImage">Update your Profile Image</option>
		<option value="deleteUser">Delete User</option>
		<option value="mergeUsers">Merge Users</option>
		<option value="createUser">Create User</option>
		<option value="updateUser">Update User</option>
		<option value="createManyUsers">Create Many Users</option>
		<option value="search">Search</option>
	</select>
	<input type="text" name="text" placeholder="Enter Search Query Here"></input></br>
	<input type="file" name="file"/>
	<input type="checkbox" name="set_sideload" value="set_sideload"/> Add Sideload </br>
	<input type="checkbox" name="external_id" value="external_id?"/>Search by external ID?</br>
	<br/>
	Enter Email Address below for authentication in Merge Users and Change Password.
	<input type="text" name="email_address" placeholder="Enter your email address here"></input>
	<input type="password" name="current_password" placeholder="Enter your current password here"></input>
	<input type="password" name="new_password" placeholder="Set or change password here"></input>
	<input type="password" name="verify_password" placeholder="Verify password here"</input></br>
	Filter by user: <input type="checkbox" name="filter_role[]" value="end-user">End-user</br>
					<input type="checkbox" name="filter_role[]" value="agent">Agent</br>
					<input type="checkbox" name="filter_role[]" value="admin">Admin</br>
					
					
	Enter fields containing any of the following user information.
	<input type="text" name="new_user_email" placeholder="Enter new user's email address here"></input>
	
	
	<input type="checkbox" name="preset_user_values">Use preset values for user fields not provided
	<button type="submit">Submit</button>
</form>

<?php
include("vendor/autoload.php");

use Zendesk\API\Client as ZendeskAPI;

// Show many users (multiple IDs) missing, I have added it
// Need to allow for multiple identieids input
// Need to get Upload user profile foto working
// Need to add fields for user input

$username = "japeterson@zendesk.com";
$subdomain = "z3nburmaglot";

$token = "FsD6L6pHGSsoHFctgl0HsPVATjEepNRHEwr2zycl"; // replace this with your token

$client = new ZendeskAPI($subdomain, $username);
$client->setAuth('token', $token); // set either token or password

$users = $client->users();
$userID = array();

// optional sideload for get requests
$userID['sideload'] = (isset($_REQUEST['set_sideload'])) ? array("groups", "organizations", "abilities", "roles", "identities") : array();

//optional filter by roles for findAll
if (!empty($_REQUEST['filter_role'])) $userID['role'] = $_REQUEST['filter_role'];
	
function print_me($user) {
	echo "<pre>";
	print_r($user);
	echo "</pre>";
}

function create_user($users){
$r_number = mt_rand(5, 150000);
return ($users->create(array("name" => (!empty($_REQUEST['name'])) ? $_REQUEST['name'] : "Test User".$r_number, 
							"external_id" => (!empty($_REQUEST['external_id'])) ? $_REQUEST['external_id'] : $r_number, 
							"alias" => (!empty($_REQUEST['alias'])) ? $_REQUEST['alias'] : "The Requester", 
							"verified" => (!empty($_REQUEST['verified'])) ? $_REQUEST['verified'] : true, 
							"locale_id" => (!empty($_REQUEST['locale_id'])) ? $_REQUEST['locale_id'] : 1, 
							"time_zone" => (!empty($_REQUEST['time_zone'])) ? $_REQUEST['time_zone'] : "Copenhagen", 
							//"email" => (!empty($_REQUEST['email'])) ? $_REQUEST['email'] : "TestEmail".$r_number."@example.com", 
							"phone" => (!empty($_REQUEST['phone'])) ? $_REQUEST['phone'] : "555-123-4567", 
							"details" => (!empty($_REQUEST['details'])) ? $_REQUEST['details'] : "address is 123 Example Ave", 
							"notes" => (!empty($_REQUEST['notes'])) ? $_REQUEST['notes'] : "This is a test user created by API", 
							"organization_id" => (!empty($_REQUEST['organization_id'])) ? $_REQUEST['organization_id'] : null, 
							"role" => (!empty($_REQUEST['role'])) ? $_REQUEST['role'] : "end-user", 
							"tags" => (!empty($_REQUEST['tags'])) ? $_REQUEST['tags'] : array("tag".$r_number), 
							"photo" => (!empty($_REQUEST['photo'])) ? $_REQUEST['photo'] : null, 
							"user_fields" => (!empty($_REQUEST['user_fields'])) ? $_REQUEST['user_fields'] : null,
							"identities" => array(array("type"=>"email", "value"=> "test".$r_number."@user.com"),
										array("type"=>"facebook", "value"=> "tester".$r_number)))));
}

switch ($_REQUEST['formBox']) {
	case "getAllUsers":
		print_me($users->findAll($userID));
		break;
	case "search":
		// options are searching by external id 
		$query = (!empty($_REQUEST['text'])) ? $_REQUEST['text'] : "15489";
		if (!empty($_REQUEST['text'])) {
			$query = $_REQUEST['text'];
			if (isset($_REQUEST["external_id?"])) $userID['external_id'] = $query;
			else $userID['query'] = $query;
			$user = $users->search($userID);							
			(!empty($user->users)) ? print_me($user) : print_r("no user found with that ID");
		} else {
			echo "Please enter a search term";
		}
		
		break;
	case "userById":
		// allows for multiple IDs to be entered and searched
		if ($_REQUEST['text'] != null){
			if (strpos($_REQUEST['text'], ',') !== false) {
				$userID['id'] = explode(',', $_REQUEST['text']); 
				foreach ($userID['id'] as $index => $value) {	$userID['id'][$index] = (int)$value; }
			} else $userID['id'] = $_REQUEST['text'];
			print_me($users->find($userID));	
		} else echo "please enter user ID";
		break;
	case "relatedinfo":
		if (!empty($_REQUEST['text'])) {
			$userID['id'] = $_REQUEST['text'];
			$user = $users->related($userID);
			($user->user_related != null) ? print_me($user) : print_r("no user found with that ID");	
		} else {
			echo "please enter a user ID";
		}
		break;
	case "mergeUsers":
		//Need to create this one
		// This shouldn't use ID but should instead use username/password		
		//  The user whose id is provided in the URL will be merged into the existing user provided in the params. Users can only merge themselves into another user
		/* curl -v -u {email_address}:{password} https://{subdomain}.zendesk.com/api/v2/users/me/merge.json \
  -H "Content-Type: application/json" -X PUT -d '{"user": {"password": "foo1234", "email": "roge@example.org"}}'
  		*/
  		


		// create a test user to merge into the credentials provided, or merge the user provided into the credentials
		if (!empty($_REQUEST['text'])){
			$test_user = $users->find(array('id' => $_REQUEST['text']));
			if (!empty($test_user)) {
			//if valid user is found create new api object for the victim user
				$client = new ZendeskAPI($subdomain, $test_user->user->email);
				$client->setAuth('token', $token); // set either token or password
			} else {
				echo "There was no user found with that ID.  Please try again.";
			}

		} else {
			$test_user = create_user($users);
			error_log($test_user->user->email);
			$new_client = new ZendeskAPI($subdomain, $test_user->user->email);
			$new_client->setAuth('token', $token); // set either token or password
		}
		if (!empty($_REQUEST['email_address']) && !empty($_REQUEST['current_password'])){
			$userID['email'] = $_REQUEST['email_address'];
			$userID['password'] = $_REQUEST['current_password'];
			print_me($new_client->users()->merge($userID));	
			// appears correct but password is being filtered.  This also occurs when performing the request via CURL
			/*
			# /storage/logs/hosts/2014/09/05/app41.pod1.ord.zdsys.com/production.log-20140905
Started PUT "/api/v2/users/me/merge.json" for 166.78.66.61 at 2014-09-05 00:10:02 +0000
Checking if user  is system user: 
Processing by Api::V2::UsersController#merge_self as JSON
  Parameters: {"user"=>{"sideload"=>nil, "email"=>"japeterson@zendesk.com", "password"=>"[FILTERED]"}}
Zendesk::Auth::Warden::BasicStrategy z3nburmaglot: Authenticated user 835498836
Checking if user 835498836 is system user: false
  Removed restricted keys ["sideload"] from parameters according to whitelist
  Filtered Parameters: {"controller"=>"api/v2/users", "action"=>"merge_self", "format"=>"json", "user"=>{"email"=>"japeterson@zendesk.com", "password"=>"[FILTERED]"}}
API request mode:basic, subdomain:z3nburmaglot, lotus:no, mobile:false, time:82, account_id:519627, user:test40014@user.com/token, url:https://z3nburmaglot.zendesk.com/api/v2/users/me/merge.json
Completed 422 Unprocessable Entity in 86.7ms (ActiveRecord: 5.5ms | https://z3nburmaglot.zendesk.com/api/v2/users/me/merge.json)
request-start: t=1409875802.012000 queue-start: 
-----------------------------------------------

# /storage/logs/hosts/2014/09/05/app2.pod1.ord.zdsys.com/production.log-20140905
Started PUT "/api/v2/users/me/merge.json" for 166.78.66.61 at 2014-09-05 00:15:44 +0000
Checking if user  is system user: 
Processing by Api::V2::UsersController#merge_self as JSON
  Parameters: {"user"=>{"password"=>"[FILTERED]", "email"=>"japeterson@zendesk.com"}}
Zendesk::Auth::Warden::BasicStrategy z3nburmaglot: Authenticated user 835498836
Checking if user 835498836 is system user: false
  Filtered Parameters: {"controller"=>"api/v2/users", "action"=>"merge_self", "format"=>"json", "user"=>{"email"=>"japeterson@zendesk.com", "password"=>"[FILTERED]"}}
API request mode:basic, subdomain:z3nburmaglot, lotus:no, mobile:false, time:42, account_id:519627, user:test40014@user.com/token, url:https://z3nburmaglot.zendesk.com/api/v2/users/me/merge.json
Completed 422 Unprocessable Entity in 44.7ms (ActiveRecord: 4.2ms | https://z3nburmaglot.zendesk.com/api/v2/users/me/merge.json)
request-start: t=1409876144.690000 queue-start: 
-----------------------------------------------
*/
		} else {
			echo "Please enter your email address and password in those fields to proceed.  The email you enter will be the surviving user and the user ID you provide will be the victim user.  If you do not enter a username a test user will be merged.";
		}

		break;
	case "createManyUsers":
		//no user input here yet :(
		$userID = array(
			array( "name" => "Rilco Woger", "email" => "example1".mt_rand(5, 150000)."@example.com", "role" => "end-user"),
			array( "name" => "Rilco Woger II", "email" => "example2".mt_rand(5, 150000)."@example.com", "role" => "end-user"),
			array( "name" => "Rilco Woger III", "email" => "example3".mt_rand(5, 150000)."@example.com", "role" => "end-user"),
			array( "name" => "Rilco Woger IV", "email" => "example4".mt_rand(5, 150000)."@example.com", "role" => "end-user")
		);
		print_me($users->createMany($userID));	
		break;
	case "updateUser":
		print_me($users->update(array("id" => (!empty($_REQUEST['text'])) ? $_REQUEST['text'] : $sample_user_id,
							"name" => (!empty($_REQUEST['name'])) ? $_REQUEST['name'] : "Roger - update #".$r_number, 
							"external_id" => (!empty($_REQUEST['external_id'])) ? $_REQUEST['external_id'] : $r_number, 
							"alias" => (!empty($_REQUEST['alias'])) ? $_REQUEST['alias'] : "The Requester", 
							"verified" => (!empty($_REQUEST['verified'])) ? $_REQUEST['verified'] : true, 
							"locale_id" => (!empty($_REQUEST['locale_id'])) ? $_REQUEST['locale_id'] : 1, 
							"time_zone" => (!empty($_REQUEST['time_zone'])) ? $_REQUEST['time_zone'] : "Copenhagen", 
							"email" => (!empty($_REQUEST['email'])) ? $_REQUEST['email'] : "TestEmail".$r_number."@example.com", 
							"phone" => (!empty($_REQUEST['phone'])) ? $_REQUEST['phone'] : "555-123-4567", 
							"details" => (!empty($_REQUEST['details'])) ? $_REQUEST['details'] : "address is 123 Example Ave", 
							"notes" => (!empty($_REQUEST['notes'])) ? $_REQUEST['notes'] : "This is a test user created by API", 
							"organization_id" => (!empty($_REQUEST['organization_id'])) ? $_REQUEST['organization_id'] : null, 
							"role" => (!empty($_REQUEST['role'])) ? $_REQUEST['role'] : "end-user", 
							"tags" => (!empty($_REQUEST['tags'])) ? $_REQUEST['tags'] : array("tag".$r_number), 
							"photo" => (!empty($_REQUEST['photo'])) ? $_REQUEST['photo'] : null, 
							"user_fields" => (!empty($_REQUEST['user_fields'])) ? $_REQUEST['user_fields'] : null)));	
	case "suspendUser":
		print_me($users->suspend(array("id" => (!empty($_REQUEST['text'])) ? $_REQUEST['text'] : ($sample_user_id), "suspended" => true)));
		break;
	case "unsuspendUser":
		print_me($users->suspend(array("id" => (!empty($_REQUEST['text'])) ? $_REQUEST['text'] : $sample_user_id, "suspended" => false)));
		break;	
	case "deleteUser":
		echo (($users->delete((!empty($_REQUEST['text'])) ? $_REQUEST['text'] : $sample_user_id)) ? ("user ".$userID['id']." was deleted successfully.") : ("user could not be deleted."));
		break;
	case "autocomplete":
		if (!empty($_REQUEST['text'])){
			$userID['name'] = $_REQUEST['text'];
			print_me($users->autocomplete($userID));
		} else {
			echo "please enter a search term";
		}
		break;
	case "updateProfileImage":
	//Returns true, but new image isn't actually uploaded.  This has required modification of the Http.php file and the way it sends data for three endpoint methods involving files (this, apps, and Voice greetings.  Please see x for more information about the modifications made.  This does not work the same as the changes made to Apps, will need to further troubleshoot.
		$userID['file'] = $_FILES['file']['tmp_name'];
		//$userID['file'] = "james.jpg";
		//$userID['type'] = "image/jpeg";
		$userID['id'] = 685284897;
		print_me($users->updateProfileImage($userID));	
		break;		
	case "me":
		print_me($users->me($userID));
		break;
	case "setPassword":
		if (!empty($_REQUEST['text']) && !empty($_REQUEST['text'])) {
			$userID['password'] = $_REQUEST['text'];
			$userID['id'] = $_REQUEST['number'];
			if ($users->setPassword($userID)) echo "user ".$userID['id']."'s password has been set.";
			else echo "user password could not be set";
		} else {
			echo "please enter a password and a user's ID";
		}
		break;	
	case "changePassword":
		if (!empty($_REQUEST['new_password']) && !empty($_REQUEST['verify_password']) && !empty($_REQUEST['current_password']) && !empty($_REQUEST['email_address'])){
			if ($_REQUEST['new_password'] === $_REQUEST['verify_password']){
				$client = new ZendeskAPI($subdomain, $_REQUEST['email_address']);
				$client->setAuth('password', $_REQUEST['current_password']); 
				($client->users()->changePassword(array('id' => $client->users()->me()->user->id, 'password' => $_REQUEST['new_password'], 'previous_password' => $_REQUEST['current_password']))->error !== null) ? print_r("Your request failed, please try again") : print_r("Your request was successful.  Password changed!");
			} else {
				echo "Please make sure the passwords in new and verify match";
			}
		} else {
			echo "please enter your email address, your new password, verify your password, and enter your old password";
		}
		break;
	case "createUser":
		print_me(create_user($users));
		break;
}


?>