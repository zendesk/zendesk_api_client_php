<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Views test class
 */
class ViewsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    protected $id;

    public function setUp() {
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
        $this->id = $view->view->id;
    }

    public function testAll() {
        $views = $this->client->views()->findAll();
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true, 'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testActive() {
        $views = $this->client->views()->findAll(array('active' => true));
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true, 'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testCompact() {
        $views = $this->client->views()->findAll(array('compact' => true));
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true, 'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $view = $this->client->view($this->id)->find();
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate() {
        $view = $this->client->view($this->id)->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
        $this->assertEquals($view->view->title, 'Roger Wilco II', 'Name of test view does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testExecute() {
        $view = $this->client->view($this->id)->execute();
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testCount() {
        $counts = $this->client->view($this->id)->count();
        $this->assertEquals(is_object($counts), true, 'Should return an object');
        $this->assertEquals(is_object($counts->view_count), true, 'Should return an object called "view_count"');
        $this->assertGreaterThan(0, $counts->view_count->view_id, 'Returns a non-numeric view_id for view_count');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testCountMany() {
        $counts = $this->client->view(array($this->id, 38568252))->count();
        $this->assertEquals(is_object($counts), true, 'Should return an object');
        $this->assertEquals(is_array($counts->view_counts), true, 'Should return an array of objects called "view_counts"');
        $this->assertGreaterThan(0, $counts->view_counts[0]->view_id, 'Returns a non-numeric view_id for view_counts[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testExport() {
        $export = $this->client->view($this->id)->export();
        $this->assertEquals(is_object($export), true, 'Should return an object');
        $this->assertGreaterThan(0, $export->export->view_id, 'Returns a non-numeric view_id for export');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testPreview() {
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
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testPreviewCount() {
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
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown() {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a view id to test with. Did setUp fail?');
        $view = $this->client->view($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
