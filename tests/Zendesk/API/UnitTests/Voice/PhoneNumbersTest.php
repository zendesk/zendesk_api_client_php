<?php

namespace Zendesk\API\UnitTests\Voice;

use Zendesk\API\UnitTests\BasicTest;

class PhoneNumbersTest extends BasicTest
{
    /**
     * Tests if the search endpoint can be accessed
     */
    public function testSearch()
    {
        $queryParams = [
            'country'   => 'US',
            'area_code' => 410,
            'contains'  => 'pizza',
            'toll_free' => 1,
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->voice->phoneNumbers()->search($queryParams);
        }, 'channels/voice/phone_numbers/search.json', 'GET', ['queryParams' => $queryParams]);
    }
}
