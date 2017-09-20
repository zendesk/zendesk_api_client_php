<?php

namespace Zendesk\API\UnitTests\Chat;

use Faker\Factory;
use Zendesk\API\UnitTests\BasicTest;

class AppsTest extends BasicTest
{
    /**
     * Tests if the client can install a Chat app
     */
    public function testInstall()
    {
        $faker = Factory::create();
        $postFields = [
            'app_id'   => $faker->numberBetween(1),
            'settings' =>
            [
                'name'      => $faker->word,
                'api_token' => $faker->md5,
            ],
        ];

        $this->assertEndpointCalled(function () use ($postFields) {
             $this->client->chat->apps()->install($postFields);
        }, 'apps/installations.json', 'POST', [
            'postFields' => $postFields,
            'apiBasePath' => '/api/chat/',
        ]);
    }
}
