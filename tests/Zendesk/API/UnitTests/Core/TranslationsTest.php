<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Translations test class
 */
class TranslationsTest extends BasicTest
{
/**
 * Test manifest method
 */
public function testFindManifest()
{
    $this->assertEndpointCalled(function () {
        $this->client->translations()->manifest();
    }, 'translations/manifest.json');
}
}
