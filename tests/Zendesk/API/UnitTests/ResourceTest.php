<?php
namespace Zendesk\API\UnitTests;

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
        $this->mockApiCall(
            'GET',
            'dummyresource.json',
            array('dummies' => true)
        );

        $this->_dummyResource->findAll();
    }

    public function testFind()
    {
        $this->mockApiCall(
            'GET',
            'dummyresource/1.json',
            array('dummy' => true)
        );

        $response = $this->_dummyResource->find(1);

        $this->assertEquals(
            isset($response->dummy),
            true,
            'Should return a response called "dummy"'
        );
    }

    public function testCreate()
    {
        $this->mockApiCall(
            'POST',
            'dummyresource.json',
            array('foo' => 'test response'),
            array(
                'bodyParams' => array(
                    'dummy' => array('foo' => 'test body')
                )
            )
        );

        $this->_dummyResource->create(
            array('foo' => 'test body')
        );

        $this->httpMock->verify();
    }

    public function testUpdate()
    {
        $this->mockApiCall(
            'PUT',
            'dummyresource/1.json',
            array('foo' => 'test response'),
            array(
                'bodyParams' => array(
                    'dummy' => array('foo' => 'test body')
                )
            )
        );

        $this->_dummyResource->update(1, array('foo' => 'test body'));
        $this->httpMock->verify();
    }

    public function testDelete()
    {
        $this->mockApiCall('DELETE', 'dummyresource/1.json', []);

        $this->_dummyResource->delete(1);
        $this->httpMock->verify();
    }
}
