<?php
namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class ResourceTest extends BasicTest
{
    private $_dummyResource;

    public function setUp()
    {
        parent::setUp();
        $this->_dummyResource = new DummyResource($this->client);
    }

    public function testFindAll()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->_dummyResource->findAll();

        $this->assertLastRequestIs([
            'method' => 'GET',
            'url' => '/api/v2/dummyresource.json',
          ]
        );
    }

    public function testFind()
    {
        $this->mockAPIResponses([
          new Response(200, [], json_encode(['dummy' => true]))
        ]);

        $this->_dummyResource->find(1);

        $this->assertLastRequestIs([
            'method' => 'GET',
            'url' => '/api/v2/dummyresource/1.json',
        ]);
    }

    public function testCreate()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $postFields = ['foo' => 'test body'];

        $this->_dummyResource->create($postFields);

        $this->assertLastRequestIs(
          [
            'method' => 'POST',
            'url' => '/api/v2/dummyresource.json',
            'postFields' => ['dummy' => $postFields]
          ]
        );
    }

    public function testUpdate()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $postFields = ['foo' => 'test body'];
        $this->_dummyResource->update(1, $postFields);

        $this->assertLastRequestIs(
          [
              'method' => 'PUT',
              'url' => '/api/v2/dummyresource/1.json',
              'postFields' => ['dummy' => $postFields],
          ]
        );
    }

    public function testDelete()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->_dummyResource->delete(1);

        $this->assertLastRequestIs(
          [
              'method' => 'DELETE',
              'url' => '/api/v2/dummyresource/1.json'
          ]
        );
    }
}
