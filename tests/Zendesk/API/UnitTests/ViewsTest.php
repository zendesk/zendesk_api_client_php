<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\Resources\Views;

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
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['views' => [['id' => $this->id]]]))
        ]);

        $views = $this->client->views()->findAllActive();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'views/active.json',
            ]
        );

        $this->assertEquals(
            is_array($views->views),
            true,
            'Should return an object containing an array called "views"'
        );
        $this->assertGreaterThan(0, $views->views[0]->id, 'Should return a numeric id for views[0]');
    }

    /**
     * Test getting compact list of views
     */
    public function testCompact()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['views' => [['id' => $this->id]]]))
        ]);

        $views = $this->client->views()->findAllCompact();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'views/compact.json',
            ]
        );
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(
            is_array($views->views),
            true,
            'Should return an object containing an array called "views"'
        );
        $this->assertGreaterThan(0, $views->views[0]->id, 'Should return a numeric id for views[0]');
    }

    /**
     * Test execution of views
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testExecute()
    {
        $queryParams = ['per_page' => 1];
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['view' => ['id' => $this->id]]))
        ]);

        $view = $this->client->views($this->id)->execute($queryParams);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'views/' . $this->id . '/execute.json',
                'queryParams' => $queryParams,
            ]
        );

        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Should return a numeric id for view');
    }

    /**
     * Test getting of view counts
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testCount()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['view_count' => ['view_id' => $this->id]]))
        ]);

        $counts = $this->client->views($this->id)->count();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'views/' . $this->id . '/count.json',
            ]
        );

        $this->assertEquals(is_object($counts), true, 'Should return an object');
        $this->assertEquals(is_object($counts->view_count), true, 'Should return an object called "view_count"');
        $this->assertGreaterThan(0, $counts->view_count->view_id, 'Should return a numeric view_id for view_count');
    }

    /**
     * Test counting many views
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testCountMany()
    {
        $queryIds = [$this->id, 80085];
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['view_counts' => [['view_id' => $this->id]]]))
        ]);

        $counts = $this->client->views($queryIds)->count();

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'views/count_many.json',
                'queryParams' => ['ids' => implode(',', $queryIds)]
            ]
        );

        $this->assertEquals(
            is_array($counts->view_counts),
            true,
            'Should return an array of objects called "view_counts"'
        );
        $this->assertGreaterThan(
            0,
            $counts->view_counts[0]->view_id,
            'Should return a numeric view_id for view_counts[0]'
        );
    }

    /**
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testExport()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['export' => ['view_id' => $this->id]]))
        ]);

        $export = $this->client->views($this->id)->export();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'views/' . $this->id . '/export.json',
            ]
        );

        $this->assertEquals(is_object($export), true, 'Should return an object');
        $this->assertGreaterThan(0, $export->export->view_id, 'Should return a numeric view_id for export');
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

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['rows' => [], 'columns' => []]))
        ]);

        $preview = $this->client->views()->preview($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'views/preview.json',
                'postFields' => [Views::OBJ_NAME => $postFields]
            ]
        );

        $this->assertEquals(is_object($preview), true, 'Should return an object');
        $this->assertEquals(is_array($preview->rows), true, 'Should return an array of objects called "rows"');
        $this->assertEquals(is_array($preview->columns), true, 'Should return an array of objects called "columns"');
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

        $this->mockAPIResponses([
            new Response(200, [], json_encode(['view_count' => (new \stdClass())]))
        ]);

        $preview = $this->client->views()->previewCount($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'views/preview/count.json',
                'postFields' => [Views::OBJ_NAME => $postFields],
            ]
        );

        $this->assertEquals(is_object($preview), true, 'Should return an object');
        $this->assertEquals(is_object($preview->view_count), true, 'Should return an object called "view_count"');
    }

    /**
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testGetTickets()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['tickets' => ['id' => $this->id]]))
        ]);

        $preview = $this->client->views($this->id)->tickets();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "views/{$this->id}/tickets.json",
            ]
        );

        $this->assertEquals(is_object($preview), true, 'Should return an object');
        $this->assertEquals(is_object($preview->tickets), true, 'Should return an object called "tickets"');
    }

    /**
     * Test creating a view
     */
    public function testCreate()
    {
        $this->mockAPIResponses([
            new Response(200, [], null)
        ]);

        $postFields = [
            'titile' => 'Test View',
            'all'    => [
                'field'    => 'status',
                'operator' => 'is',
                'value'    => 'open'
            ]
        ];

        $this->client->views($this->id)->create($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'views.json',
                'postFields' => [Views::OBJ_NAME => $postFields],
            ]
        );
    }

    /**
     * Test updating a view
     */
    public function testUpdate()
    {
        $this->mockAPIResponses([
            new Response(200, [], null)
        ]);

        $postFields = [
            'titile' => 'Test View',
            'all'    => [
                'field'    => 'status',
                'operator' => 'is',
                'value'    => 'open'
            ]
        ];

        $this->client->views()->update($this->id, $postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => "views/{$this->id}.json",
                'postFields' => [Views::OBJ_NAME => $postFields],
            ]
        );
    }

    /**
     * Test listing views
     */
    public function testFindAll()
    {
        $this->mockAPIResponses([
            new Response(200, [], null)
        ]);

        $this->client->views($this->id)->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'views.json',
            ]
        );
    }

    /**
     * Test finding a view
     */
    public function testFind()
    {
        $this->mockAPIResponses([
            new Response(200, [], null)
        ]);

        $this->client->views($this->id)->find();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "views/{$this->id}.json",
            ]
        );
    }

    /**
     * Test deleting a view
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testDelete()
    {
        $this->mockAPIResponses([
            new Response(200, [], null)
        ]);

        $this->client->views($this->id)->delete();

        $this->assertLastRequestIs(
            [
                'method'   => 'DELETE',
                'endpoint' => "views/{$this->id}.json",
            ]
        );
    }
}
