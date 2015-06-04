<?php
namespace Zendesk\API\LiveTests;

class ResourceTest extends BasicTest
{
	private $_dummyResource;

	public function setUp()
	{
		$this->_dummyResource = new DummyResource($this->client);
		parent::setUp();
	}

    public function testFindAll()
    {
        $this->mockApiCall('GET', '/dummyresource.json',
            array('dummies' => true));

        $this->_dummyResource->findAll();
    }

	public function testFind()
	{
        $this->mockApiCall('GET', '/dummyresource/1.json',
            array('dummy' => true));

		$response = $this->_dummyResource->find(1);

        $this->assertEquals(isset($response->dummy), true, 'Should return a response called "dummy"');
	}

	public function testCreate()
	{
        $this->mockApiCall('POST', '/dummyresource.json',
            array('dummy' => true));

		$response = $this->_dummyResource->create(array('test' => 'foo', 'what' => 'is'));

        $this->assertEquals(isset($response->dummy), true, 'Should return a response called "dummy"');
	}

	public function testUpdate()
	{
        $this->mockApiCall('PUT', '/dummyresource/1.json',
            array('dummy' => true));

		$response = $this->_dummyResource->update(1, []);

        $this->assertEquals(isset($response->dummy), true, 'Should return a response called "dummy"');
	}

	public function testDelete()
	{
        $this->mockApiCall('DELETE', '/dummyresource/1.json', []);

		$this->_dummyResource->delete(1);
	}
}
