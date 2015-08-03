<?php

namespace Zendesk\API\UnitTests\HelpCenter;

use Zendesk\API\Resources\HelpCenter\Categories;
use Zendesk\API\UnitTests\BasicTest;

class CategoriesTest extends BasicTest
{
    /**
     * Tests if the locale is added to the route
     */
    public function testRoutesWithLocale()
    {
        $categoriesResource = $this->client->helpCenter->categories();
        $categoriesResource->setLocale('en-US');

        $this->assertEndpointCalled(function () use ($categoriesResource) {
            $categoriesResource->findAll();
        }, 'help_center/en-US/categories.json');

        $this->assertEndpointCalled(function () use ($categoriesResource) {
            $categoriesResource->find(1);
        }, 'help_center/en-US/categories/1.json');

        $postFields = [
            'position'    => 2,
            'locale'      => 'en-us',
            'name'        => 'Super Hero Tricks',
            'description' => 'This category contains a collection of super hero tricks',
        ];

        $this->assertEndpointCalled(function () use ($categoriesResource, $postFields) {
            $categoriesResource->create($postFields);
        }, 'help_center/en-US/categories.json', 'POST', ['postFields' => ['category' => $postFields]]);

        $this->assertEndpointCalled(function () use ($categoriesResource, $postFields) {
            $categoriesResource->update(1, $postFields);
        }, 'help_center/en-US/categories/1.json', 'PUT', ['postFields' => ['category' => $postFields]]);
    }

    /**
     * Tests if the route can be generated
     */
    public function testRouteWithoutLocale()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->categories()->findAll();
        }, 'help_center/categories.json');
    }

    /**
     * Tests if the Update category source locale endpoint can be called and passed the correct params
     */
    public function testUpdateCategorySourceLocale()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->categories()->updateSourceLocale(1, 'fr');
        }, 'help_center/categories/1/source_locale.json', 'PUT', ['postFields' => ['category_locale' => 'fr']]);
    }

    /**
     * Tests if the Update category source locale endpoint can be called and passed the correct params
     */
    public function testUpdateCategorySourceLocaleNoId()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->categories(1)->updateSourceLocale(null, 'fr');
        }, 'help_center/categories/1/source_locale.json', 'PUT', ['postFields' => ['category_locale' => 'fr']]);
    }
}
