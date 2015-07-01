<?php
namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class ResourceTest extends BasicTest
{
    private $dummyResource;

    public function setUp()
    {
        parent::setUp();
        $this->dummyResource = new DummyResource($this->client);
    }

    public function testFindAll()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->dummyResource->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'dummy_resource.json',
        ]);
    }

    public function testFind()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['dummy' => true]))
        ]);

        $this->dummyResource->find(1);

        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'dummy_resource/1.json',
            'queryParams' => []
        ]);
    }

    public function testCanSetIteratorParams()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['dummy' => true]))
        ]);

        $iterators = ['per_page' => 1, 'page' => 2, 'sort_order' => 'desc', 'sort_by' => 'date'];

        $this->dummyResource->findAll($iterators);


        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'dummy_resource.json',
            'queryParams' => $iterators
        ]);
    }

    public function testCreate()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postFields = ['foo' => 'test body'];

        $this->dummyResource->create($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'dummy_resource.json',
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
        $this->dummyResource->update(1, $postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'dummy_resource/1.json',
                'postFields' => ['dummy' => $postFields],
            ]
        );
    }

    public function testDelete()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->dummyResource->delete(1);

        $this->assertLastRequestIs(
            [
                'method'   => 'DELETE',
                'endpoint' => 'dummy_resource/1.json'
            ]
        );
    }

    public function testSideLoad()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['dummy' => true]))
        ]);

        $sideloads = ['foo', 'bar', 'hello', 'world'];
        $this->dummyResource->sideload($sideloads);
        $this->dummyResource->findAll();

        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'dummy_resource.json',
            'queryParams' => ['include' => implode(',', $sideloads)]
        ]);
    }

    public function testCreateMany()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postFields = [['foo' => 'test body'], ['foo2' => 'test body 2'], ['foo3' => 'test body3']];

        $this->dummyResource->createMany($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'dummy_resource/create_many.json',
                'postFields' => [DummyResource::OBJ_NAME_PLURAL => $postFields]
            ]
        );
    }

    public function testFindMany()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['dummy' => true]))
        ]);

        $ids = [1, 2, 3, 4, 5];
        $this->dummyResource->findMany($ids);

        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'dummy_resource/show_many.json',
            'queryParams' => ['ids' => implode(',', $ids)]
        ]);
    }

    public function testUpdateManySameData()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $ids        = [1, 2, 3, 4, 5];
        $postFields = ['foo' => 'test body'];

        $this->dummyResource->updateMany(array_merge(['ids' => $ids], $postFields));

        $this->assertLastRequestIs(
            [
                'method'      => 'PUT',
                'endpoint'    => 'dummy_resource/update_many.json',
                'queryParams' => ['ids' => implode(',', $ids)],
                'postFields'  => [DummyResource::OBJ_NAME => $postFields],
            ]
        );
    }

    public function testUpdateManyDifferentData()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postFields = [
            ['id' => 1, 'foo' => 'bar', 'hello' => 'world'],
            ['id' => 2, 'foo' => 'bar', 'hello' => 'world'],
            ['id' => 3, 'foo' => 'bar', 'hello' => 'world'],
            ['id' => 4, 'foo' => 'bar', 'hello' => 'world']
        ];

        $this->dummyResource->updateMany($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'dummy_resource/update_many.json',
                'postFields' => [DummyResource::OBJ_NAME_PLURAL => $postFields],
            ]
        );
    }

    public function testDeleteMany()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $ids = [1, 2, 3, 4, 5];
        $this->dummyResource->deleteMany($ids);

        $this->assertLastRequestIs(
            [
                'method'      => 'DELETE',
                'endpoint'    => 'dummy_resource/destroy_many.json',
                'queryParams' => ['ids' => implode(',', $ids)]
            ]
        );
    }

    /**
     * @expectedException Zendesk\API\Exceptions\ApiResponseException
     * @expectedExceptionMessage Zendesk may be experiencing internal issues or undergoing scheduled maintenance.
     */
    public function testHandlesServerException()
    {
        $this->mockApiResponses(
            new Response(500, [], '')
        );

        $this->dummyResource->create(['foo' => 'bar']);
    }

    /**
     * @expectedException Zendesk\API\Exceptions\ApiResponseException
     * @expectedExceptionMessage Unprocessable Entity
     */
    public function testHandlesApiException()
    {
        $this->mockApiResponses(
            new Response(422, [], '')
        );

        $this->dummyResource->create(['foo' => 'bar']);
    }
}
