<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <title>Zendesk PHP API Client</title>
    <style>
        .zengreen {
            color: #78A300;
        }
        li{
          padding-bottom: 4px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 well">
            <h1 class="zengreen">Zendesk</h1>
            <h5>To help you get started, coding examples for authentication and basic usage are available in the `samples` folder.</h5>

            <div class="clearfix">&nbsp;</div>
            <strong>Authentication</strong>
            <ul>
              <li><a href="samples/auth/oauth.php">Sample oAuth flow.</a></li>
            </ul>

            <strong>Groups</strong>
            <ul>
              <li><a href="samples/groups/createGroup.php">Create a new group</a></li>
              <li><a href="samples/groups/getGroups.php">Retrieve all groups</a></li>
            </ul>

            <strong>Tickets</strong>
            <ul>
              <li><a href="samples/tickets/qcreateTicket.php">Create a new ticket.</a></li>
              <li><a href="samples/tickets/createTicket.php">Create a new ticket with the requester's email address, if the requester's identity doesn't exist.</a></li>
              <div class="clearfix">&nbsp;</div>

              <li><a href="samples/tickets/getTickets.php">Retrieve all tickets.</a></li>
              <li><a href="samples/tickets/viewTicket.php">Retrieve ticket details</a></li>
              <li><a href="samples/tickets/searchTickets.php">Retrieve tickets from the end-user email address</a></li>
              <div class="clearfix">&nbsp;</div>

              <li><a href="samples/tickets/updateTicket.php">Update a ticket</a></li>
              <li><a href="samples/tickets/deleteTicket.php">Delete a ticket</a></li>
            </ul>


            <strong>Users</strong>
            <ul>
              <li><a href="samples/users/createUser.php">Create a new end-user</a></li>
              <li><a href="samples/users/getUsers.php">Retrieve all end-users</a></li>
              <li><a href="samples/users/searchUser.php">Search end-user</a></li>
            </ul>

            <div class="clearfix">&nbsp;</div>

        </div>
    </div>
</div>
</body>
</html>
