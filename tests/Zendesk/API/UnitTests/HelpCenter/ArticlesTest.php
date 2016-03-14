<?php

namespace Zendesk\API\UnitTests\HelpCenter;

use Zendesk\API\Resources\HelpCenter\Articles;
use Zendesk\API\UnitTests\BasicTest;

class ArticlesTest extends BasicTest
{
    /**
     * Tests if the locale is added to the route
     */
    public function testRoutesWithLocale()
    {
        $articlesResource = $this->client->helpCenter->articles();
        $articlesResource->setLocale('en-US');

        $this->assertEndpointCalled(function () use ($articlesResource) {
            $articlesResource->findAll();
        }, 'help_center/en-US/articles.json');

        $this->assertEndpointCalled(function () use ($articlesResource) {
            $articlesResource->find(1);
        }, 'help_center/en-US/articles/1.json');

        $postFields = [
            'title' => 'TeSTING',
            'locale' => 'en-us',
            'body' => 'This category contains a collection of super hero tricks',
        ];

        $this->assertEndpointCalled(function () use ($articlesResource, $postFields) {
            $articlesResource->create($postFields);
        }, 'help_center/en-US/articles.json', 'POST', ['postFields' => ['articles' => $postFields]]);

        $this->assertEndpointCalled(function () use ($articlesResource, $postFields) {
            $articlesResource->update(1, $postFields);
        }, 'help_center/en-US/articles/1.json', 'PUT', ['postFields' => ['articles' => $postFields]]);
    }

    /**
     * Tests if the route can be generated
     */
    public function testRouteWithoutLocale()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->articles()->findAll();
        }, 'help_center/articles.json');
    }

    /**
     * Tests if the Update category source locale endpoint can be called and passed the correct params
     */
    public function testUpdateArticlesSourceLocale()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->articles()->updateSourceLocale(1, 'en-us');
        }, 'help_center/articles/1/source_locale.json', 'PUT', ['postFields' => ['article_locale' => 'en-us']]);
    }

    /**
     * Tests if the Update category source locale endpoint can be called and passed the correct params
     */
    public function testUpdateArticlesourceLocaleNoId()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->articles(1)->updateSourceLocale(null, 'en-us');
        }, 'help_center/articles/1/source_locale.json', 'PUT', ['postFields' => ['article_locale' => 'en-us']]);
    }
}
