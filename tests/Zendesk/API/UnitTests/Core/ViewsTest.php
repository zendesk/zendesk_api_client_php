<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\Resources\Core\Views;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Views test class
 */
class ViewsTest extends BasicTest
{
    /**
     * @var int
     */
    protected $id = 12345;

    /**
     * Test getting all active views
     */
    public function testActive()
    {
        $this->assertEndpointCalled(function () {
            $this->client->views()->findAllActive();
        }, 'views/active.json');
    }

    /**
     * Test getting compact list of views
     */
    public function testCompact()
    {
        $this->assertEndpointCalled(function () {
            $this->client->views()->findAllCompact();
        }, 'views/compact.json');
    }

    /**
     * Test execution of views
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testExecute()
    {
        $queryParams = ['per_page' => 1];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->views($this->id)->execute($queryParams);
        }, "views/{$this->id}/execute.json", 'GET', ['queryParams' => $queryParams]);
    }

    /**
     * Test getting of view counts
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testCount()
    {
        $this->assertEndpointCalled(function () {
            $this->client->views($this->id)->count();
        }, "views/{$this->id}/count.json");
    }

    /**
     * Test counting many views
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testCountMany()
    {
        $queryIds = [$this->id, 80085];

        $this->assertEndpointCalled(function () use ($queryIds) {
            $this->client->views($queryIds)->count();
        }, 'views/count_many.json', 'GET', ['queryParams' => ['ids' => implode(',', $queryIds)]]);
    }

    /**
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testExport()
    {
        $this->assertEndpointCalled(function () {
            $this->client->views()->export(['id' => $this->id]);
        }, "views/{$this->id}/export.json", 'GET', ['queryParams' => []]);
    }

    /**
     * Test previewing of views
     */
    public function testPreview()
    {
        $postFields = [
            'all'    => [
                [
                    'operator' => 'is',
                    'value'    => 'open',
                    'field'    => 'status'
                ]
            ],
            'output' => [
                'columns' => ['subject']
            ]
        ];

        $this->assertEndpointCalled(function () use ($postFields) {
            $this->client->views()->preview($postFields);
        }, 'views/preview.json', 'POST', ['postFields' => ['view' => $postFields]]);
    }

    /**
     * Test getting the count of a view preview
     */
    public function testPreviewCount()
    {
        $postFields = [
            'all'    => [
                [
                    'operator' => 'is',
                    'value'    => 'open',
                    'field'    => 'status'
                ]
            ],
            'output' => [
                'columns' => ['subject']
            ]
        ];

        $this->assertEndpointCalled(function () use ($postFields) {
            $this->client->views()->previewCount($postFields);
        }, 'views/preview/count.json', 'POST', ['postFields' => ['view' => $postFields]]);
    }

    /**
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testGetTickets()
    {
        $this->assertEndpointCalled(function () {
            $this->client->views($this->id)->tickets();
        }, "views/{$this->id}/tickets.json");
    }

    /**
     * Test views search
     */
    public function testViewsSearch()
    {
        $queryParams = [
            'active' => true,
            'access' => 'shared',
            'query' => 'foo'
        ];
        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->views()->search($queryParams);
        }, "views/search.json", 'GET', ['queryParams' => $queryParams]);
    }
}
