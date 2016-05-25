<?php

namespace Zendesk\API\UnitTests\HelpCenter;

use Zendesk\API\Resources\HelpCenter\Categories;
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
    }

    /**
     * Test if the route can be generated
     */
    public function testRouteWithLocale()
    {
        $this->assertEndpointCalled(function () {
            $this->client->helpCenter->articles()->findAll();
        }, 'help_center/articles.json');
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
}
