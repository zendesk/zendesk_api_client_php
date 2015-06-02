<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Attachments test class
 */
class AppsTests extends BasicTest
{

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    public function testUploadApps()
    {
        $apps = new \Zendesk\API\Apps($this->client);
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $file = ['file' => '@' . getcwd() . '/tests/assets/app.zip'];
        } else {
            $file = ['file' => curl_file_create(getcwd() . '/tests/assets/app.zip')];
        }

        $upload = $apps->upload($file);

        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->assertEquals(is_object($upload), true, 'Should return an object');
        $this->assertEquals(is_integer($upload->id), true, 'Should return an integer called "id"');
        $stack = array($upload);

        return $stack;
    }

    /**
     * @depends testUploadApps
     */
    public function testCreateApps(array $stack)
    {
        $upload = array_pop($stack);
        $apps = new \Zendesk\API\Apps($this->client);

        $create = $apps->create([
            'name' => 'TESTING APP' . rand(),
            'short_description' => 'testing',
            'upload_id' => (string)$upload->id
        ]);
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '202', 'Does not return HTTP code 202');
        $this->assertEquals(is_object($create), true, 'Should return an object');
        $this->assertEquals(is_string($create->job_id), true, 'Should return a string called "job_id"');
        $stack = array($create);

        return $stack;
    }

    /**
     * @depends testCreateApps
     */
    public function testJobStatusApps(array $stack)
    {
        $jobStatus = array_pop($stack);
        $apps = new \Zendesk\API\Apps($this->client);

        $jobStatus = $apps->jobStatus(['id' => (string)$jobStatus->job_id]);
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->assertEquals(is_object($jobStatus), true, 'Should return an object');
        $this->assertEquals(is_string($jobStatus->id), true, 'Should return a string called "job_id"');
        $this->assertEquals(isset($jobStatus->url), true, 'Should return a "url"');
        $this->assertEquals(is_string($jobStatus->status), true, 'Should return a string called "status"');
        $stack = array($jobStatus);

        return $stack;
    }

    /**
     * @depends testJobStatusApps
     */
    public function testKeepTestingJobStatusUntilDone(array $stack)
    {
        $jobStatus = array_pop($stack);
        $apps = new \Zendesk\API\Apps($this->client);

        do {
            $response = $apps->jobStatus(['id' => (string)$jobStatus->id]);
            $status = $response->status;

            if ($status === 'failed') {
                throw new \Exception($response->message);
            } elseif ($status === 'queued' || $status === 'working') {
                sleep(5);
            }
        } while ($status === 'queued' || $status === 'working');

        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals(is_integer($response->app_id), true, 'Should return a string called "app_id"');

        $stack = array($response);

        return $stack;
    }

    /**
     * @depends testKeepTestingJobStatusUntilDone
     */
    public function testUpdateApps(array $stack)
    {
        $jobStatus = array_pop($stack);
        $apps = new \Zendesk\API\Apps($this->client);

        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $file = ['id' => $jobStatus->app_id, 'file' => '@' . getcwd() . '/tests/assets/app.zip'];
        } else {
            $file = ['id' => $jobStatus->app_id, 'file' => curl_file_create(getcwd() . '/tests/assets/app.zip')];
        }

        $upload = $apps->update($file);
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->assertEquals(is_object($upload), true, 'Should return an object');
        $this->assertEquals(is_integer($upload->id), true, 'Should return an integer called "id"');
        $this->assertEquals(is_string($upload->name), true, 'Should return a string called "name"');
        $stack = array($upload);

        return $stack;
    }

    /**
     * @depends testUpdateApps
     */
    public function testDeleteApps(array $stack)
    {
        $app = array_pop($stack);
        $apps = new \Zendesk\API\Apps($this->client);

        $delete = $apps->delete(['id' => $app->id]);
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->assertEquals(is_bool($delete), true, 'Should return an object');
        $this->assertEquals($delete, true, 'Should return true');
    }
}
