<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Views test class
 */
class ViewsTest extends \PHPUnit_Framework_TestCase {

    private $client;
    private $subdomain;
    private $username;
    private $password;
    private $token;
    private $oAuthToken;

    public function __construct() {
        $this->subdomain = $GLOBALS['SUBDOMAIN'];
        $this->username = $GLOBALS['USERNAME'];
        $this->password = $GLOBALS['PASSWORD'];
        $this->token = $GLOBALS['TOKEN'];
        $this->oAuthToken = $GLOBALS['OAUTH_TOKEN'];
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('token', $this->token);
    }

    public function testCredentials() {
        $this->assertEquals($GLOBALS['SUBDOMAIN'] != '', true, 'Expecting GLOBALS[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['TOKEN'] != '', true, 'Expecting GLOBALS[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['USERNAME'] != '', true, 'Expecting GLOBALS[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $requests = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAll() {
        $views = $this->client->views()->findAll();
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true, 'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testActive() {
        $views = $this->client->views()->findAll(array('active' => true));
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true, 'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCompact() {
        $views = $this->client->views()->findAll(array('compact' => true));
        $this->assertEquals(is_object($views), true, 'Should return an object');
        $this->assertEquals(is_array($views->views), true, 'Should return an object containing an array called "views"');
        $this->assertGreaterThan(0, $views->views[0]->id, 'Returns a non-numeric id for views[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $view = $this->client->view(38568232)->find(); // don't delete view #38568232
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $view = $this->client->views()->create(array(
            'title' => 'Roger Wilco',
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
        $id = $view->view->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $view = $this->client->view($id)->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
        $this->assertEquals($view->view->title, 'Roger Wilco II', 'Name of test view does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $view->view->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a view id to test with. Did testCreate fail?');
        $view = $this->client->view($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testExecute() {
        $view = $this->client->view(38568232)->execute();
        $this->assertEquals(is_object($view), true, 'Should return an object');
        $this->assertEquals(is_object($view->view), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $view->view->id, 'Returns a non-numeric id for view');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCount() {
        $counts = $this->client->view(38568232)->count();
        $this->assertEquals(is_object($counts), true, 'Should return an object');
        $this->assertEquals(is_object($counts->view_count), true, 'Should return an object called "view_count"');
        $this->assertGreaterThan(0, $counts->view_count->view_id, 'Returns a non-numeric view_id for view_count');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCountMany() {
        $counts = $this->client->view(array(38568232, 38568252))->count();
        $this->assertEquals(is_object($counts), true, 'Should return an object');
        $this->assertEquals(is_array($counts->view_counts), true, 'Should return an array of objects called "view_counts"');
        $this->assertGreaterThan(0, $counts->view_counts[0]->view_id, 'Returns a non-numeric view_id for view_counts[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testExport() {
        $export = $this->client->view(38568232)->export();
        $this->assertEquals(is_object($export), true, 'Should return an object');
        $this->assertGreaterThan(0, $export->export->view_id, 'Returns a non-numeric view_id for export');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
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

    /**
     * @depends testAuthToken
     */
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

}

?>
