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
              <li><a href="samples/tickets/createTicketWithAttachment.php">Create a new ticket with attachment.</a></li>
              <li><a href="samples/tickets/createTicket.php">Create a new ticket with the requester's email address, if the requester's identity doesn't exist.</a></li>
              <div class="clearfix">&nbsp;</div>

              <li><a href="samples/tickets/getTickets.php">Retrieve all tickets.</a></li>
              <li><a href="samples/tickets/viewTicket.php">Retrieve ticket details</a></li>
              <li><a href="samples/tickets/searchTickets.php">Retrieve tickets from the end-user email address</a></li>
              <li><a href="samples/tickets/getTicketComments.php">Get all comments from a related ticket</a>
              <div class="clearfix">&nbsp;</div>

              <li><a href="samples/tickets/updateTicket.php">Update a ticket</a></li>
              <li><a href="samples/tickets/deleteTicket.php">Delete a ticket</a></li>
            </ul>
            
            <strong>Help Center</strong>
            <ul>
              <li><a href="samples/helpcenter/createArticles.php">Create an article</a></li>
              <li><a href="samples/helpcenter/createSections.php">Create a section</a></li>
              <li><a href="samples/helpcenter/findArticles.php">Find an article</a></li>
              <li><a href="samples/helpcenter/findCategories.php">Find a category</a></li>
              <li><a href="samples/helpcenter/findSections.php">Find a section</a></li>
            </ul>

            <strong>Ticket Fields</strong>
            <ul>
              <li><a href="samples/ticket_fields/createDropdownOption.php">Create a new dropdown option for an existing dropdown custom ticket field</a></li>
              <li><a href="samples/ticket_fields/editDropdownOption.php">Update an existing dropdown option for a dropdown custom ticket field</a></li>
              <li><a href="samples/ticket_fields/replaceDropdownOptions.php">Replace all existing dropdown options for a dropdown custom ticket field</a></li>
            </ul>

            <strong>Organizations</strong>           
            <ul>
              <li><a href="samples/organizations/createOrganization.php">Create a new organization</a></li>
            </ul>

            <strong>Users</strong>
            <ul>
              <li><a href="samples/users/createUser.php">Create a new end-user</a></li>
              <li><a href="samples/users/getUsers.php">Retrieve all end-users</a></li>
              <li><a href="samples/users/searchUser.php">Search end-user</a></li>
            </ul>

            <strong>Attachments</strong>
            <ul>
              <li><a href="samples/attachments/uploadFileAttachment.php">Upload a file attachment</a></li>
              <li><a href="samples/attachments/uploadStreamAttachment.php">Upload a stream attachment</a></li>
            </ul>

            <div class="clearfix">&nbsp;</div>

        </div>
    </div>
</div>
</body>
</html>
