<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Translations test class
 */
class LocalesTest extends BasicTest
{
/**
 * Test manifest method
 */
public function testFindManifest()
{
    $this->assertEndpointCalled(function () {
        $this->client->translations()->manifest();
    }, 'translations/slack-auth-service/manifest.json');
}
}