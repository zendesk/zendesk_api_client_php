<?php

namespace Zendesk\API\UnitTests\Sell;

use Faker\Factory;
use Zendesk\API\UnitTests\BasicTest;

class ContactsTest extends BasicTest
{

    /**
     * Test that the correct traits were added by checking the available methods
     */
    public function testMethods()
    {
        $this->assertTrue(method_exists($this->client->sell->contacts(), 'create'));
        $this->assertTrue(method_exists($this->client->sell->contacts(), 'delete'));
        $this->assertTrue(method_exists($this->client->sell->contacts(), 'find'));
        $this->assertTrue(method_exists($this->client->sell->contacts(), 'findAll'));
        $this->assertTrue(method_exists($this->client->sell->contacts(), 'update'));
        $this->assertTrue(method_exists($this->client->sell->contacts(), 'upsert'));
    }

    /**
     * Tests if the upsert endpoint can be called and passed the correct params
     */
    public function testUpsert()
    {
        $faker = Factory::create();

        $queryParams = [
            'email' => $faker->email,
            'phone' => $faker->phoneNumber,
        ];

        $postFields = [
            'email'         => $faker->email,
            'custom_fields' => [
                'Some Field' => $faker->text
            ]
        ];

        $encodedQueryParams = [];
        foreach ($queryParams as $key => $value) {
            // Encode the 'phone' query param's whitespace
            if ($key === 'phone') {
                $value = str_replace(' ', '%20', $value);
            }
            $encodedQueryParams[$key] = $value;
        }

        $this->assertEndpointCalled(function () use ($queryParams, $postFields) {
            $this->client->sell->contacts()->upsert($queryParams, $postFields);
        }, '/contacts/upsert', 'POST', [
            'queryParams' => $encodedQueryParams,
            'postFields'  => ['data' => $postFields],
            'apiBasePath' => '/v2'
        ]);
    }
}
