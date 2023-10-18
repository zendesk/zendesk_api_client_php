<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Traits\Utility\CbpIterator;

class MockTickets {
    public function findAll($params)
    {
        static $callCount = 0;

        // Simulate two pages of tickets
        $tickets = $callCount === 0
            ? [['id' => 1], ['id' => 2]]
            : [['id' => 3], ['id' => 4]];

        // Simulate a cursor for the next page on the first call
        $afterCursor = $callCount === 0 ? 'cursor_for_next_page' : null;

        $callCount++;

        return (object) [
            'tickets' => $tickets,
            'meta' => (object) [
                'has_more' => $afterCursor !== null,
                'after_cursor' => $afterCursor,
            ],
        ];
    }
}

class CbpIteratorTest extends BasicTest
{
    public function testFetchesTickets()
    {
        $mockTickets = new MockTickets;
        $iterator = new CbpIterator($mockTickets, 2);

        $tickets = iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
    }
}
