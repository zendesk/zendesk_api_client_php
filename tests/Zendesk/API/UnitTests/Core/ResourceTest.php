<?php
namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\LazyOpenStream;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Class ResourceTest
 */
class ResourceTest extends BasicTest
{
    private $dummyResource;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->dummyResource = new DummyResource($this->client);
    }

    /**
     * Test findAll method
     */
    public function testFindAll()
    {
        $this->assertEndpointCalled(function () {
            $this->dummyResource->findAll();
        }, 'dummy_resource.json');
    }

    /**
     * Test find method
     */
    public function testFind()
    {
        $resourceId = 8282;
        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->dummyResource->find($resourceId);
        }, "dummy_resource/{$resourceId}.json");
    }

    /**
     * Test we can set the iterator parameters
     */
    public function testCanSetIteratorParams()
    {
        $iterators = ['per_page' => 1, 'page' => 2, 'sort_order' => 'desc', 'sort_by' => 'date'];

        $this->assertEndpointCalled(function () use ($iterators) {
            $this->dummyResource->findAll($iterators);
        }, 'dummy_resource.json', 'GET', ['queryParams' => $iterators]);
    }

    /**
     * This tests if passing `include`, or `sideload` is converted to a single `include` query parameter, also tests if
     * other params can be set
     */
    public function testCanSetAdditionalParams()
    {
        $params = [
            'include'     => ['users', 'groups'],
            'sideload'    => ['test', 'this'],
            'external_id' => 12345
        ];

        $this->assertEndpointCalled(function () use ($params) {
            $this->dummyResource->findAll($params);
        }, 'dummy_resource.json', 'GET', [
            'queryParams' => [
                'include'     => 'users,groups,test,this',
                'external_id' => 12345
            ]
        ]);
    }

    /**
     * Test create method
     */
    public function testCreate()
    {
        $postFields = ['foo' => 'test body'];

        $this->assertEndpointCalled(
            function () use ($postFields) {
                $this->dummyResource->create($postFields);
            },
            'dummy_resource.json',
            'POST',
            ['postFields' => ['dummy' => $postFields]]
        );
    }

    /**
     * Test update method
     */
    public function testUpdate()
    {
        $resourceId = 39392;
        $postFields = ['foo' => 'test body'];
        $this->assertEndpointCalled(
            function () use ($resourceId, $postFields) {
                $this->dummyResource->update($resourceId, $postFields);
            },
            "dummy_resource/{$resourceId}.json",
            'PUT',
            ['postFields' => ['dummy' => $postFields]]
        );
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {

        $resourceId = 292;
        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->dummyResource->delete($resourceId);
        }, "dummy_resource/{$resourceId}.json", 'DELETE');
    }

    /**
     * Test setting of sideloads
     */
    public function testSideLoad()
    {
        $sideloads = ['foo', 'bar', 'hello', 'world'];

        $this->assertEndpointCalled(function () use ($sideloads) {
            $this->dummyResource->sideload($sideloads);
            $this->dummyResource->findAll();
        }, 'dummy_resource.json', 'GET', ['queryParams' => ['include' => implode(',', $sideloads)]]);
    }

    /**
     * Test createMany method
     */
    public function testCreateMany()
    {
        $postFields = [['foo' => 'test body'], ['foo2' => 'test body 2'], ['foo3' => 'test body3']];

        $this->assertEndpointCalled(
            function () use ($postFields) {
                $this->dummyResource->createMany($postFields);
            },
            'dummy_resource/create_many.json',
            'POST',
            ['postFields' => ['dummies' => $postFields]]
        );
    }

    /**
     * Test findMany method
     */
    public function testFindMany()
    {
        $ids = [1, 2, 3, 4, 5];
        $this->assertEndpointCalled(function () use ($ids) {
            $this->dummyResource->findMany($ids);
        }, 'dummy_resource/show_many.json', 'GET', ['queryParams' => ['ids' => implode(',', $ids)]]);
    }

    /**
     * Test updateMany with the same data
     */
    public function testUpdateManySameData()
    {
        $ids        = [1, 2, 3, 4, 5];
        $postFields = ['foo' => 'test body'];

        $this->assertEndpointCalled(
            function () use ($ids, $postFields) {
                $this->dummyResource->updateMany(array_merge(['ids' => $ids], $postFields));
            },
            'dummy_resource/update_many.json',
            'PUT',
            [
                'queryParams' => ['ids' => implode(',', $ids)],
                'postFields'  => ['dummy' => $postFields],
            ]
        );
    }

    /**
     * Test updateMany with different data
     */
    public function testUpdateManyDifferentData()
    {
        $postFields = [
            ['id' => 1, 'foo' => 'bar', 'hello' => 'world'],
            ['id' => 2, 'foo' => 'bar', 'hello' => 'world'],
            ['id' => 3, 'foo' => 'bar', 'hello' => 'world'],
            ['id' => 4, 'foo' => 'bar', 'hello' => 'world']
        ];

        $this->assertEndpointCalled(
            function () use ($postFields) {
                $this->dummyResource->updateMany($postFields);
            },
            'dummy_resource/update_many.json',
            'PUT',
            ['postFields' => ['dummies' => $postFields]]
        );
    }

    /**
     * Test delete many
     */
    public function testDeleteMany()
    {
        $ids = [1, 2, 3, 4, 5];

        $this->assertEndpointCalled(
            function () use ($ids) {
                $this->dummyResource->deleteMany($ids);
            },
            'dummy_resource/destroy_many.json',
            'DELETE',
            ['queryParams' => ['ids' => implode(',', $ids)]]
        );
    }

    /**
     * Test multipost upload trait creates an upload method.
     */
    public function testUpload()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $params = [
            'file' => getcwd() . '/tests/assets/UK.png'
        ];

        $this->dummyResource->upload($params);

        $this->assertLastRequestIs(
            [
                'method'    => 'POST',
                'endpoint'  => 'dummy_resource/uploads.json',
                'multipart' => true,
            ]
        );
    }

    /**
     * Test we can handle server exceptions
     *
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
     * Test we can handle api exceptions
     *
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

    /**
     * Test we can handle api exceptions when no response is returned from the API
     *
     * @expectedException Zendesk\API\Exceptions\ApiResponseException
     * @expectedExceptionMessage Error completing request
     */
    public function testHandlesEmptyResponse()
    {
        // Create an exception object which is thrown when a response couldn't be retrieved
        $unsuccessfulResponse = RequestException::create(new Request('GET', 'foo'), null);
        $this->mockApiResponses($unsuccessfulResponse);

        $this->dummyResource->create(['foo' => 'bar']);
    }

    /**
     * Test if the correct User-Agent header is passed method
     */
    public function testUserAgent()
    {
        $this->mockApiResponses([
            new Response(200, [], '')
        ]);

        $this->dummyResource->findAll();

        $transaction = $this->mockedTransactionsContainer[0];
        $request     = $transaction['request'];

        $this->assertRegExp('/ZendeskAPI PHP/', $request->getHeaderLine('User-Agent'));
    }

    /**
     * Tests if extra headers are set
     */
    public function testAdditionalHeaders()
    {
        $this->client->setHeader('X-CUSTOM-HEADER', 'foo');

        $this->mockApiResponses([
            new Response(200, [], '')
        ]);

        $this->dummyResource->findAll();

        $transaction = $this->mockedTransactionsContainer[0];
        $request     = $transaction['request'];

        $this->assertEquals('foo', $request->getHeaderLine('X-CUSTOM-HEADER'));
    }

    /**
     * Tests the ability to create or update many
     */
    public function testCreateOrUpdateMany()
    {
        $postFields = [
            'resources' =>
                [
                    ['foo' => 'test body'],
                    ['foo2' => 'test body 2'],
                    ['foo3' => 'test body3']
                ]
        ];

        $this->assertEndpointCalled(
            function () use ($postFields) {
                $this->dummyResource->createOrUpdateMany($postFields);
            },
            'dummy_resource/create_or_update_many.json',
            'POST',
            ['postFields' => ['dummies' => $postFields]]
        );
    }

    /**
     * Test multipart upload with streams
     */
    public function testUploadStreamMultiPart()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $params = [
            'file' => new LazyOpenStream(getcwd() . '/tests/assets/UK.png', 'r')
        ];

        $this->dummyResource->upload($params);

        $this->assertLastRequestIs(
            [
                'method' => 'POST',
                'endpoint' => 'dummy_resource/uploads.json',
                'multipart' => true,
            ]
        );
    }
}
