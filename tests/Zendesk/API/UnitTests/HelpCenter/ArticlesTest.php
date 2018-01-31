<?php

namespace Zendesk\API\UnitTests\HelpCenter;

use Faker\Factory;
use Zendesk\API\UnitTests\BasicTest;

class ArticlesTest extends BasicTest
{
    /**
     * Tests if the locale is added to the route
     */
    public function testRoutesWithLocale()
    {
        $faker = Factory::create();
        $locale = $faker->locale;

        $articlesResource = $this->client->helpCenter->articles();
        $articlesResource->setLocale($locale);

        $this->assertEndpointCalled(function () use ($articlesResource) {
            $articlesResource->findAll();
        }, "help_center/{$locale}/articles.json");

        $sectionId = $faker->numberBetween(1);
        $this->assertEndpointCalled(function () use ($sectionId, $locale) {
            $resource = $this->client->helpCenter->sections($sectionId)->articles();
            $resource->setLocale($locale)->findAll();
        }, "help_center/{$locale}/sections/{$sectionId}/articles.json");

        $articleId = $faker->numberBetween(1);
        $this->assertEndpointCalled(function () use ($articlesResource, $articleId) {
            $articlesResource->find($articleId);
        }, "help_center/{$locale}/articles/{$articleId}.json");
    }

    /**
     * Test if the route can be generated
     */
    public function testRouteWithoutLocale()
    {
        $faker = Factory::create();
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->articles()->findAll();
        }, 'help_center/articles.json');

        $sectionId = $faker->numberBetween(1);
        $this->assertEndpointCalled(function () use ($sectionId) {
            $this->client->helpCenter->sections($sectionId)->articles()->findAll();
        }, "help_center/sections/{$sectionId}/articles.json");
    }

    /**
     * Test if bulk attachments can be called and pass the correct params
     */
    public function testBulkAttachments()
    {
        $this->assertEndpointCalled(function () {
            $attachments = [10002, 10003];
            $this->client->helpCenter->articles()->bulkAttach(1, $attachments);
        }, 'help_center/articles/1/bulk_attachments.json', 'POST');
    }


    /**
     * Tests if the Update article source locale endpoint can be called and passed the correct params
     */
    public function testUpdateArticleSourceLocale()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->articles()->updateSourceLocale(1, 'fr');
        }, 'help_center/articles/1/source_locale.json', 'PUT', ['postFields' => ['article_locale' => 'fr']]);
    }

    /**
     * Tests if the Update article source locale endpoint can be called and passed the correct params
     */
    public function testUpdateArticleSourceLocaleNoId()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->articles(1)->updateSourceLocale(null, 'fr');
        }, 'help_center/articles/1/source_locale.json', 'PUT', ['postFields' => ['article_locale' => 'fr']]);
    }

    /**
     * Tests if the Search on Articles accesses the correct endpoint and that it uses the params provided
     */
    public function testSearch()
    {
        $faker = Factory::create();
        $params = ['query' => $faker->word];

        $this->assertEndpointCalled(function () use ($params) {
            $this->client->helpCenter->articles()->search($params);
        }, 'help_center/articles/search.json', 'GET', ['queryParams' => $params]);
    }

    /**
     * Test if Create article endpoint is called with correct params.
     */
    public function testCreate()
    {
        $faker = Factory::create();
        $sectionId = $faker->numberBetween(1);
        $params = ['title' => $faker->word, 'locale' => 'en'];

        $this->assertEndpointCalled(function () use ($params, $sectionId) {
            $this->client->helpCenter->sections($sectionId)->articles()->create($params);
        }, "help_center/sections/{$sectionId}/articles.json", 'POST', [
            'postFields' => ['article' => $params],
        ]);
    }
}
