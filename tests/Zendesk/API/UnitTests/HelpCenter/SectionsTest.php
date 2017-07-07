<?php

namespace Zendesk\API\UnitTests\HelpCenter;

use Faker\Factory;
use Zendesk\API\Resources\HelpCenter\Sections;
use Zendesk\API\UnitTests\BasicTest;

class SectionsTest extends BasicTest
{
    /**
     * Tests if the locale is added to the route
     */
    public function testRoutesWithLocale()
    {
        $faker = Factory::create();
        $locale = $faker->locale;

        $sectionsResource = $this->client->helpCenter->sections();
        $sectionsResource->setLocale($locale);

        $this->assertEndpointCalled(function () use ($sectionsResource) {
            $sectionsResource->findAll();
        }, "help_center/{$locale}/sections.json");

        $categoryId = $faker->numberBetween(1);
        $this->assertEndpointCalled(function () use ($categoryId, $locale) {
            $resource = $this->client->helpCenter->categories($categoryId)->sections();
            $resource->setLocale($locale)->findAll();
        }, "help_center/{$locale}/categories/{$categoryId}/sections.json");

        $sectionId = $faker->numberBetween(1);
        $this->assertEndpointCalled(function () use ($sectionsResource, $sectionId) {
            $sectionsResource->find($sectionId);
        }, "help_center/{$locale}/sections/{$sectionId}.json");

        $postFields = [
            'position' => $faker->numberBetween(1),
            'locale' => $locale,
            'name' => $faker->sentence,
            'description' => $faker->sentence,
        ];

        $this->assertEndpointCalled(function () use ($sectionsResource, $postFields, $sectionId) {
            $sectionsResource->update($sectionId, $postFields);
        }, "help_center/{$locale}/sections/{$sectionId}.json", 'PUT', [
            'postFields' => ['section' => $postFields]
        ]);
    }

    /**
     * Tests if the route can be generated
     */
    public function testRouteWithoutLocale()
    {
        $faker = Factory::create();
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->sections()->findAll();
        }, 'help_center/sections.json');

        $categoryId = $faker->numberBetween(1);
        $this->assertEndpointCalled(function () use ($categoryId) {
            $this->client->helpCenter->categories($categoryId)->sections()->findAll();
        }, "help_center/categories/{$categoryId}/sections.json");
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

    /**
     * Tests if the Create section endpoint can be called and passed the correct params
     */
    public function testCanCreateSection()
    {
        $faker = Factory::create();
        $params = [
            'position' => $faker->numberBetween(1),
            'name' => $faker->sentence,
            'description' => $faker->sentence,
            'locale' => $faker->locale,
        ];
        $categoryId = $faker->numberBetween(1);

        $this->assertEndpointCalled(function () use ($params, $categoryId) {
            $this->client->helpCenter->categories($categoryId)->sections()->create($params);
        }, "help_center/categories/{$categoryId}/sections.json", 'POST', [
            'postFields' => ['section' => $params]
        ]);
    }
}
