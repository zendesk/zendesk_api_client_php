<html>
Hi there, please choose an option -
<h4>Users</h4>
<form action="users.php" method="POST" enctype="multipart/form-data">
	<select name="formBox">
		<option value="getAllUsers">getAllUsers</option>
		<option value="suspendUser">SuspendUser</option>
		<option value="unsuspendUser">UnsuspendUser</option>
		<option value="relatedInfo">Get Related Info</option>
		<option value="byGroup">Get Users By Group</option>
		<option value="byOrg">Get Users By Organization</option>
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
		<option value="searchByExternalID">Search By External ID</option>
	</select>
	<input type="text" name="text" placeholder="Enter Search Query Here"></input>
	<input type="text" name="number" placeholder="Enter User ID here"></input>
	<input type="file" name="file"/>
	<br/>
	<input type="password" name="current_password" placeholder="Enter your current password here"></input>
	<input type="password" name="new_password" placeholder="Set or change password here"></input>
	<input type="password" name="verify_password" placeholder="Verify password here"</input>
	<button type="submit">Submit</button>
</form>

<h4>Satisfaction Ratings</h4>
<form action="satisfaction.php" method="POST">
	<select name="formBox">
		<option value="getAllRatings">Get All Satisfaction Ratings</option>
		<option value="getRatingById">Get a Satisfaction Rating By Id</option>
		<option value="createRating">Create a new Rating</option>
	</select>
	<button type="submit">Submit</button>
</form>

<h4>Locales</h4>
<form action="locales.php" method="POST">
	<select name="formBox">
		<option value="getAllLocales">Get All Locales</option>
		<option value="getAgentLocale">Get Locales Localized for Agent</option>
		<option value="localeById">Get Locale by ID</option>
		<option value="detectBest">Detect Best Locale for User</option>
		<option value="currentLocale">Get Locale of Current User</option>
	</select>
	<button type="submit">Submit</button>
</form>

<h4>Attachments</h4>
<form action="attachments.php" method="POST" enctype="multipart/form-data">
	<select name="formBox">
		<option value="getAttachment">Get Attachment by ID</option>
		<option value="deleteAttachment">Delete Attachment by ID</option>
		<option value="uploadAttachment">Upload Attachment</option>
		<option value="redactAttachment">Redact Attachment</option>
	</select>
	<input type="text" name="attachment_id" placeholder="attachment ID"/>
	<input type="file" name="file"/>
	<button type="submit">Submit</button>
</form>

<h4>Tickets</h4>
<form action="tickets.php" method="POST">
	<select name="formBox">
		<option value="createTicket">Create Ticket</option>
	</select>
	<button type="submit">Submit</button>
</form>

<h4>Apps</h4>
<form action="apps.php" method="POST" enctype="multipart/form-data">
	<select name="formBox">
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

<h4>Side-Loading</h4>
Unfortunately this does not exist in wrapper, either as a separate class or as a method/parameter of existing classes.  Maybe it should be added or not?



<h4>OAuth Clients</h4>
<form action="oauthclients.php" method="POST">
	<select name="formBox">
		<option value="getAllClients">Get All OAuth Clients</option>
		<option value="getClient">Get an OAuth Client</option>
		<option value="createClient">Create an OAuth Client</option>
		<option value="updateClient">Update an OAuth Client</option>
		<option value="deleteClient">Delete an OAuth Client</option>
	</select>
	<input type="text" placeholder="OAuth Client ID" name="clientID"></input>
	<button type="submit">Submit</button>
</form>


<h4>OAuth Tokens</h4>
<form action="oauthtokens.php" method="POST">
	<select name="formBox">
		<option value="getAllTokens">Get All OAuth Tokens</option>
		<option value="getToken">Get an OAuth Token</option>
		<option value="deleteToken">Delete an OAuth Token</option>
	</select>
	<input type="text" placeholder="OAuth Token ID" name="tokenID"></input>
	<button type="submit">Submit</button>
</form>

</html>




