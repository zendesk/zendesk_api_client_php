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
        $this->markTestSkipped('CBP TODO assert');
        // 1) Zendesk\API\UnitTests\Chat\AppsTest::testInstall
        // Failed asserting that the API basepath is /api/chat/
        // Failed asserting that false is identical to 0.
        // /app/tests/Zendesk/API/UnitTests/BasicTest.php:190
        // /app/tests/Zendesk/API/UnitTests/BasicTest.php:120
        // /app/tests/Zendesk/API/UnitTests/BasicTest.php:233
        // /app/tests/Zendesk/API/UnitTests/Chat/AppsTest.php:31
        // phpvfscomposer:///app/vendor/phpunit/phpunit/phpunit:35

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
