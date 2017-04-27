<?php

namespace Zendesk\API\UnitTests;

use Faker\Factory;
use Zendesk\API\HttpClient;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Class VersionTest
 */
class VersionTest extends BasicTest
{
    /**
     * Test that the versions used across the package are consistent
     */
    public function testVersionsAreConsistent()
    {
        $faker = Factory::create();

        $fileVersion = trim(file_get_contents(__DIR__ . '/../../../../VERSION'));
        $client = new HttpClient($faker->word);

        $this->assertEquals("ZendeskAPI PHP {$fileVersion}", $client->getUserAgent());
    }
}
