<?php

namespace Zendesk\API\UnitTests\Voice;

use Zendesk\API\UnitTests\BasicTest;

class TicketsTest extends BasicTest
{

    /**
     * Tests if the search endpoint can be accessed
     */
    public function testCreate()
    {

        $postFields = [
            'display_to_agent' => 1744920396,
            'ticket' => [
              'via_id' => 45,
              'subject'  => 'The quick brown fox jumps over the lazy dog',
              'comment'  => [
                  'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor'
                            . ' incididunt ut labore et dolore magna aliqua.'
              ],
              'priority' => 'normal',
              'id'       => '12345',
            ],
          ];

        $this->assertEndpointCalled(function () use ($postFields) {
            $this->client->voice->tickets()->create($postFields);
        }, 'channels/voice/tickets.json', 'POST', ['postFields' => $postFields]);
    }
}
