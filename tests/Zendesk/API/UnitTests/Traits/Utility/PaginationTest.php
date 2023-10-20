<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\Traits\Utility\Pagination\CbpStrategy;
use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Traits\Utility\Pagination\PaginationIterator;

class MockResource {
    private $resources;
    private $resourceName;
    private $callCount = 0;

    public function __construct($resourceName, $resources)
    {
        $this->resourceName = $resourceName;
        $this->resources = $resources;
        $this->callCount = 0;
    }

    public function findAll($params)
    {
        // Simulate two pages of resources
        $resources = $this->callCount === 0
            ? $this->resources[0]
            : $this->resources[1];

        // Simulate a cursor for the next page on the first call
        $afterCursor = $this->callCount === 0 ? 'cursor_for_next_page' : null;

        $this->callCount++;

        return (object) [
            $this->resourceName => $resources,
            'meta' => (object) [
                'has_more' => $afterCursor !== null,
                'after_cursor' => $afterCursor,
            ],
        ];
    }
}

class PaginationTest extends BasicTest
{
    public function testFetchesTickets()
    {
        $mockTickets = new MockResource('tickets', [
            [['id' => 1], ['id' => 2]],
            [['id' => 3], ['id' => 4]]
        ]);
        $strategy = new CbpStrategy($mockTickets, 'tickets', 2);
        $iterator = new PaginationIterator($strategy);

        $tickets = iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
    }

    public function testFetchesUsers()
    {
        $mockUsers = new MockResource('users', [
            [['id' => 1, 'name' => 'User 1'], ['id' => 2, 'name' => 'User 2']],
            [['id' => 3, 'name' => 'User 3'], ['id' => 4, 'name' => 'User 4']]
        ]);
        $strategy = new CbpStrategy($mockUsers, 'users', 2);
        $iterator = new PaginationIterator($strategy);

        $users = iterator_to_array($iterator);

        $this->assertEquals([
            ['id' => 1, 'name' => 'User 1'],
            ['id' => 2, 'name' => 'User 2'],
            ['id' => 3, 'name' => 'User 3'],
            ['id' => 4, 'name' => 'User 4']
        ], $users);
    }
}

// agl "use (FindAll|Default)" src
// src/Zendesk/API/Resources/Core/Activities.php
// src/Zendesk/API/Resources/Core/AppInstallationLocations.php
// src/Zendesk/API/Resources/Core/AppInstallations.php
// src/Zendesk/API/Resources/Core/AppLocations.php
// src/Zendesk/API/Resources/Core/AuditLogs.php
// src/Zendesk/API/Resources/Core/Automations.php
// src/Zendesk/API/Resources/Core/Bookmarks.php
// src/Zendesk/API/Resources/Core/Brands.php
// src/Zendesk/API/Resources/Core/CustomRoles.php
// src/Zendesk/API/Resources/Core/DynamicContentItems.php
// src/Zendesk/API/Resources/Core/DynamicContentItemVariants.php
// src/Zendesk/API/Resources/Core/GroupMemberships.php
// src/Zendesk/API/Resources/Core/Groups.php
// src/Zendesk/API/Resources/Core/Locales.php
// src/Zendesk/API/Resources/Core/Macros.php
// src/Zendesk/API/Resources/Core/OAuthClients.php
// src/Zendesk/API/Resources/Core/OAuthTokens.php
// src/Zendesk/API/Resources/Core/OrganizationFields.php
// src/Zendesk/API/Resources/Core/OrganizationMemberships.php
// src/Zendesk/API/Resources/Core/Organizations.php
// src/Zendesk/API/Resources/Core/OrganizationSubscriptions.php
// src/Zendesk/API/Resources/Core/OrganizationTickets.php
// src/Zendesk/API/Resources/Core/RequestComments.php
// src/Zendesk/API/Resources/Core/Requests.php
// src/Zendesk/API/Resources/Core/SatisfactionRatings.php
// src/Zendesk/API/Resources/Core/Sessions.php
// src/Zendesk/API/Resources/Core/SharingAgreements.php
// src/Zendesk/API/Resources/Core/SlaPolicies.php
// src/Zendesk/API/Resources/Core/SupportAddresses.php
// src/Zendesk/API/Resources/Core/SuspendedTickets.php
// src/Zendesk/API/Resources/Core/Tags.php
// src/Zendesk/API/Resources/Core/Targets.php
// src/Zendesk/API/Resources/Core/TicketAudits.php
// src/Zendesk/API/Resources/Core/TicketComments.php
// src/Zendesk/API/Resources/Core/TicketFields.php
// src/Zendesk/API/Resources/Core/TicketFieldsOptions.php
// src/Zendesk/API/Resources/Core/TicketForms.php
// src/Zendesk/API/Resources/Core/TicketMetrics.php
// src/Zendesk/API/Resources/Core/Tickets.php
// src/Zendesk/API/Resources/Core/Translations.php
// src/Zendesk/API/Resources/Core/Triggers.php
// src/Zendesk/API/Resources/Core/TwitterHandles.php
// src/Zendesk/API/Resources/Core/UserFields.php
// src/Zendesk/API/Resources/Core/UserIdentities.php
// src/Zendesk/API/Resources/Core/Users.php
// src/Zendesk/API/Resources/Core/Views.php
// src/Zendesk/API/Resources/Core/Webhooks.php
// src/Zendesk/API/Resources/HelpCenter/Articles.php
// src/Zendesk/API/Resources/HelpCenter/Categories.php
// src/Zendesk/API/Resources/HelpCenter/Sections.php
// src/Zendesk/API/Resources/Voice/PhoneNumbers.php
// src/Zendesk/API/Traits/Resource/Defaults.php
