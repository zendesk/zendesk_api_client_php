<?php

namespace Zendesk\API\UnitTests\HelpCenter;

use Zendesk\API\Resources\HelpCenter\Sections;
use Zendesk\API\UnitTests\BasicTest;

class SectionsTest extends BasicTest
{
    /**
     * Tests if the locale is added to the route
     */
    public function testRoutesWithLocale()
    {
        $sectionsResource = $this->client->helpCenter->sections();
        $sectionsResource->setLocale('en-US');

        $this->assertEndpointCalled(function () use ($sectionsResource) {
            $sectionsResource->findAll();
        }, 'help_center/en-US/sections.json');

        $this->assertEndpointCalled(function () use ($sectionsResource) {
            $sectionsResource->find(1);
        }, 'help_center/en-US/sections/1.json');

        $postFields = [
            'position'    => 2,
            'locale'      => 'en-us',
            'name'        => 'Super Hero Tricks',
            'description' => 'This section contains a collection of super hero tricks',
        ];

        $this->assertEndpointCalled(function () use ($sectionsResource, $postFields) {
            $sectionsResource->create($postFields);
        }, 'help_center/en-US/sections.json', 'POST', ['postFields' => ['section' => $postFields]]);

        $this->assertEndpointCalled(function () use ($sectionsResource, $postFields) {
            $sectionsResource->update(1, $postFields);
        }, 'help_center/en-US/sections/1.json', 'PUT', ['postFields' => ['section' => $postFields]]);
    }

    /**
     * Tests if the route can be generated
     */
    public function testRouteWithoutLocale()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->sections()->findAll();
        }, 'help_center/sections.json');
    }

    /**
     * Tests if the Update section source locale endpoint can be called and passed the correct params
     */
    public function testUpdateSectionSourceLocale()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->sections()->updateSourceLocale(1, 'fr');
        }, 'help_center/sections/1/source_locale.json', 'PUT', ['postFields' => ['section_locale' => 'fr']]);
    }

    /**
     * Tests if the Update section source locale endpoint can be called and passed the correct params
     */
    public function testUpdateSectionSourceLocaleNoId()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->sections(1)->updateSourceLocale(null, 'fr');
        }, 'help_center/sections/1/source_locale.json', 'PUT', ['postFields' => ['section_locale' => 'fr']]);
    }
}
