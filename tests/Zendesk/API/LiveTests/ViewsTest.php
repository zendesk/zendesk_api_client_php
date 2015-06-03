<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Views test class
 */
class ViewsTest extends BasicTest
{
    protected $id = 12345;

    public function testCreate()
    {
        $this->mockApiCall('POST', '/views.json', array('view' => array('id' => $this->id, 'title' => 'Roger Wilco')), array('code' => 201));

        $view = $this->client->views()->create(array(
            'title' => 'Roger Wilco',
            'active' => true,
            'all' => array(
                array(
                    'field' => 'status',
                    'operator' => 'is',
                    'value' => 'open'
                ),
                array(
                    'field' => 'priority',
                    'operator' => 'less_than',
                    'value' => 'high'
                )
            ),
            'any' => array(
                array(
                    'field' => 'current_tags',
                    'operator' => 'includes',
                    'value' => 'hello'
                )
            )
        ));
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
        $this->assertEquals($view->view->title, 'Roger Wilco', 'Title of test view does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
    }

    public function testAll()
    {
        $this->mockApiCall('GET', '/views.json?', array('views' => array(array('id' => $this->id))));

        $views = $this->client->views()->findAll();
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true,
            'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
    }

    public function testActive()
    {
        $this->mockApiCall('GET', '/views/active.json?', array('views' => array(array('id' => $this->id))));

        $views = $this->client->views()->findAll(array('active' => true));
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true,
            'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
    }

    public function testCompact()
    {
        $this->mockApiCall('GET', '/views/compact.json?', array('views' => array(array('id' => $this->id))));
        $views = $this->client->views()->findAll(array('compact' => true));
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true,
            'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
    }

    public function testFind()
    {
        $this->mockApiCall('GET', '/views/' . $this->id . '.json?', array('view' => array('id' => $this->id)));
        $view = $this->client->view($this->id)->find();
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
    }

    public function testUpdate()
    {
        $this->mockApiCall('PUT', '/views/' . $this->id . '.json', array('view' => array('id' => $this->id, 'title' => 'Roger Wilco II')));

        $view = $this->client->view($this->id)->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
        $this->assertEquals($view->view->title, 'Roger Wilco II', 'Name of test view does not match');
    }

    public function testExecute()
    {
        $this->mockApiCall('GET', '/views/' . $this->id . '/execute.json?', array('view' => array('id' => $this->id)));

        $view = $this->client->view($this->id)->execute();
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
    }

    public function testCount()
    {
        $this->mockApiCall('GET', '/views/' . $this->id . '/count.json?', array('view_count' => array('view_id' => $this->id)));

        $counts = $this->client->view($this->id)->count();
        $this->assertEquals(is_object($counts), true, 'Should return an object');
        $this->assertEquals(is_object($counts->view_count), true, 'Should return an object called "view_count"');
        $this->assertGreaterThan(0, $counts->view_count->view_id, 'Returns a non-numeric view_id for view_count');
    }

    public function testCountMany()
    {
        $this->mockApiCall('GET', '/views/count_many.json?ids=' . implode(',', array($this->id, 80085)) . '&', array('view_counts' => array(array('view_id' => $this->id))));

        $counts = $this->client->view(array($this->id, 80085))->count();
        $this->assertEquals(is_object($counts), true, 'Should return an object');
        $this->assertEquals(is_array($counts->view_counts), true,
            'Should return an array of objects called "view_counts"');
        $this->assertGreaterThan(0, $counts->view_counts[0]->view_id,
            'Returns a non-numeric view_id for view_counts[0]');
    }

    public function testExport()
    {
        $this->mockApiCall('GET', '/views/' . $this->id . '/export.json?', array('export' => array('view_id' => $this->id)));

        $export = $this->client->view($this->id)->export();
        $this->assertEquals(is_object($export), true, 'Should return an object');
        $this->assertGreaterThan(0, $export->export->view_id, 'Returns a non-numeric view_id for export');
    }

    public function testPreview()
    {
        $this->mockApiCall('POST', '/views/preview.json', array('rows' => array(), 'columns' => array()));

        $preview = $this->client->views()->preview(array(
            'all' => array(
                array(
                    'operator' => 'is',
                    'value' => 'open',
                    'field' => 'status'
                )
            ),
            'output' => array(
                'columns' => array('subject')
            )
        ));
        $this->assertEquals(is_object($preview), true, 'Should return an object');
        $this->assertEquals(is_array($preview->rows), true, 'Should return an array of objects called "rows"');
        $this->assertEquals(is_array($preview->columns), true, 'Should return an array of objects called "columns"');
    }

    public function testPreviewCount()
    {
        $this->mockApiCall('POST', '/views/preview/count.json', array('view_count' => (new \stdClass())));

        $preview = $this->client->views()->previewCount(array(
            'all' => array(
                array(
                    'operator' => 'is',
                    'value' => 'open',
                    'field' => 'status'
                )
            ),
            'output' => array(
                'columns' => array('subject')
            )
        ));
        $this->assertEquals(is_object($preview), true, 'Should return an object');
        $this->assertEquals(is_object($preview->view_count), true, 'Should return an object called "view_count"');
    }

    public function testDelete()
    {
        $this->mockApiCall('DELETE', '/views/' . $this->id . '.json?', array());
        $this->client->views($this->id)->delete();
    }
}
