<?php

namespace Zendesk\API\UnitTests;

use Zendesk\API\Client;
use Zendesk\API\Http;

//(Client $client, $endPoint, $json = array(), $method = 'GET', $contentType = 'application/json') {

/**
 * Http test class
 */
class HttpTest extends BasicTest
{
    public function setUp()
    {
        Http::$curl = new MockCurlRequest();
    }

    public function testGetRequest()
    {
        $response = Http::send($this->client, 'tickets.json');

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL}, $this->client->getApiUrl() . Http::prepare('tickets.json') . '?',
            'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_CUSTOMREQUEST}, 'GET', 'Should be a GET');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }

    public function testGetRequestWithQuery()
    {
        $response = Http::send($this->client, 'tickets.json', ['check' => 1]);

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL},
            $this->client->getApiUrl() . Http::prepare('tickets.json') . '?check=1', 'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_CUSTOMREQUEST}, 'GET', 'Should be a GET');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }

    public function testGetRequestWithContentType()
    {
        $response = Http::send($this->client, 'tickets.json', [], 'GET', 'application/x-www-form-urlencoded');

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL}, $this->client->getApiUrl() . Http::prepare('tickets.json') . '?',
            'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_CUSTOMREQUEST}, 'GET', 'Should be a GET');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/x-www-form-urlencoded', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }

    public function testPostRequest()
    {
        $response = Http::send($this->client, 'tickets.json', [], 'POST');

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL}, $this->client->getApiUrl() . Http::prepare('tickets.json'),
            'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_POST}, true, 'Should be a POST');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }

    public function testPostRequestWithData()
    {
        $response = Http::send($this->client, 'tickets.json', ['check' => 1], 'POST');

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL}, $this->client->getApiUrl() . Http::prepare('tickets.json'),
            'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_POST}, true, 'Should be a POST');
        $this->assertEquals($response->{CURLOPT_POSTFIELDS}, '{"check":1}', 'Should have POST data');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }

    public function testPostRequestWithContentType()
    {
        $response = Http::send($this->client, 'tickets.json', ['check' => 1], 'POST',
            'application/x-www-form-urlencoded');

        $data = new \StdClass;
        $data->check = 1;

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL}, $this->client->getApiUrl() . Http::prepare('tickets.json'),
            'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_POST}, true, 'Should be a POST');
        $this->assertEquals($response->{CURLOPT_POSTFIELDS}, $data, 'Should have POST data');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/x-www-form-urlencoded', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }

    public function testPutRequest()
    {
        $response = Http::send($this->client, 'tickets.json', [], 'PUT');

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL}, $this->client->getApiUrl() . Http::prepare('tickets.json'),
            'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_CUSTOMREQUEST}, 'PUT', 'Should be a PUT');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }

    public function testPutRequestWithData()
    {
        $response = Http::send($this->client, 'tickets.json', ['check' => 1], 'PUT');

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL}, $this->client->getApiUrl() . Http::prepare('tickets.json'),
            'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_CUSTOMREQUEST}, 'PUT', 'Should be a PUT');
        $this->assertEquals($response->{CURLOPT_POSTFIELDS}, '{"check":1}', 'Should have PUT data');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }

    public function testPutRequestWithContentType()
    {
        $response = Http::send($this->client, 'tickets.json', ['check' => 1], 'PUT',
            'application/x-www-form-urlencoded');

        $data = new \StdClass;
        $data->check = 1;

        $this->assertEquals(is_object($response), true, 'Should return an object');
        $this->assertEquals($response->{CURLOPT_URL}, $this->client->getApiUrl() . Http::prepare('tickets.json'),
            'Should be the correct url');
        $this->assertEquals($response->{CURLOPT_CUSTOMREQUEST}, 'PUT', 'Should be a PUT');
        $this->assertEquals($response->{CURLOPT_POSTFIELDS}, $data, 'Should have POST data');
        $this->assertContains('Accept: application/json', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Accept header');
        $this->assertContains('Content-Type: application/x-www-form-urlencoded', $response->{CURLOPT_HTTPHEADER},
            'Should contain a Content-Type header');
    }
}
