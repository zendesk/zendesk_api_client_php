<?php

namespace Zendesk\API\UnitTests;

use Zendesk\API\Resources\Views;

/**
 * Views test class
 */
class ViewsTest extends BasicTest
{
    protected $id = 12345;

    public function testActive()
    {
        $this->mockApiCall(
          'GET',
          'views/active.json',
          ['views' => [['id' => $this->id]]]
        );

        $views = $this->client->views()->findAll(['active' => true]);
        $this->httpMock->verify();
        $this->assertEquals(is_array($views->views), true,
            'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
    }

    public function testCompact()
    {
        $this->mockApiCall(
          'GET',
          'views/compact.json',
          ['views' => [['id' => $this->id]]]
        );
        $views = $this->client->views()->findAll(['compact' => true]);
        $this->httpMock->verify();
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true,
            'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
    }

    public function testExecute()
    {
        $queryParams = ['per_page' => 1];
        $this->mockApiCall(
          'GET',
          'views/' . $this->id . '/execute.json',
          ['view' => ['id' => $this->id]],
          ['queryParams' => $queryParams]
        );

        $view = $this->client->views($this->id)->execute($queryParams);
        $this->httpMock->verify();
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
    }

    public function testCount()
    {
        $this->mockApiCall(
          'GET',
          'views/' . $this->id . '/count.json',
          ['view_count' => ['view_id' => $this->id]]
        );

        $counts = $this->client->views($this->id)->count();
        $this->httpMock->verify();
        $this->assertEquals(is_object($counts), true, 'Should return an object');
        $this->assertEquals(is_object($counts->view_count), true, 'Should return an object called "view_count"');
        $this->assertGreaterThan(0, $counts->view_count->view_id, 'Returns a non-numeric view_id for view_count');
    }

    public function testCountMany()
    {
        $queryIds = [$this->id, 80085];
        $this->mockApiCall(
          'GET',
          'views/count_many.json',
          ['view_counts' => [['view_id' => $this->id]]],
          ['queryParams' => ['ids' => implode(',' , $queryIds)]]
        );

        $counts = $this->client->views($queryIds)->count();
        $this->httpMock->verify();
        $this->assertEquals(is_array($counts->view_counts), true,
            'Should return an array of objects called "view_counts"');
        $this->assertGreaterThan(0, $counts->view_counts[0]->view_id,
            'Returns a non-numeric view_id for view_counts[0]');
    }

    public function testExport()
    {
        $this->mockApiCall(
          'GET',
          'views/' . $this->id . '/export.json',
          ['export' => ['view_id' => $this->id]]
        );

        $export = $this->client->views($this->id)->export();
        $this->httpMock->verify();
        $this->assertEquals(is_object($export), true, 'Should return an object');
        $this->assertGreaterThan(0, $export->export->view_id, 'Returns a non-numeric view_id for export');
    }

    public function testPreview()
    {
        $bodyParams = [
          'all'    => [
            [
              'operator' => 'is',
              'value'    => 'open',
              'field'    => 'status'
            ]
          ],
          'output' => [
            'columns' => [ 'subject' ]
          ]
        ];

        $this->mockApiCall(
          'POST',
          'views/preview.json',
          ['rows' => [], 'columns' => []],
          ['bodyParams' => [Views::OBJ_NAME => $bodyParams]]
        );

        $preview = $this->client->views()->preview($bodyParams);
        $this->httpMock->verify();
        $this->assertEquals(is_object($preview), true, 'Should return an object');
        $this->assertEquals(is_array($preview->rows), true, 'Should return an array of objects called "rows"');
        $this->assertEquals(is_array($preview->columns), true, 'Should return an array of objects called "columns"');
    }

    public function testPreviewCount()
    {
        $bodyParams = [
            'all' => [
                [
                    'operator' => 'is',
                    'value' => 'open',
                    'field' => 'status'
                ]
            ],
            'output' => [
                'columns' => ['subject']
            ]
        ];

        $this->mockApiCall(
          'POST',
          'views/preview/count.json',
          ['view_count' => (new \stdClass())],
          ['bodyParams' => [Views::OBJ_NAME => $bodyParams]]
        );

        $preview = $this->client->views()->previewCount($bodyParams);
        $this->httpMock->verify();
        $this->assertEquals(is_object($preview), true, 'Should return an object');
        $this->assertEquals(is_object($preview->view_count), true, 'Should return an object called "view_count"');
    }

    public function testGetTickets()
    {
        $this->mockApiCall(
          'GET',
          "views/{$this->id}/tickets.json",
          ['tickets' => ['id' => $this->id]]
        );

        $preview = $this->client->views($this->id)->tickets();
        $this->httpMock->verify();
        $this->assertEquals(is_object($preview), true, 'Should return an object');
        $this->assertEquals(is_object($preview->tickets), true, 'Should return an object called "tickets"');
    }
}
