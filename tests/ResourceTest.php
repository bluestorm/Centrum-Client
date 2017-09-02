<?php

use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Exceptions\AttributeDoesNotExistException;
use Bluestorm\Centrum\Request;
use Bluestorm\Centrum\Resource;
use Bluestorm\Centrum\ResourceNotFoundException;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
	protected $resource = 'website';
	protected $resourceObject;

	protected static $createdResourceId;

	public function setUp()
	{
		$this->resourceObject = new Resource('test', json_decode("{
      \"_id\": \"599fe2b1433aca29a1a3f306\",
      \"index\": 0,
      \"guid\": \"68fbd6d3-3079-43d6-9313-e7fe89c464c5\",
      \"isActive\": false,
      \"balance\": \"$2,367.97\",
      \"picture\": \"http://placehold.it/32x32\",
      \"age\": 23,
      \"eyeColor\": \"green\",
      \"name\": {
        \"first\": \"Davidson\",
        \"last\": \"Zimmerman\"
      },
      \"company\": \"ROCKLOGIC\"
    }", true));

		if(!Centrum::isAvailable())
		{
			$this->fail('API must be available to run tests.');
		}
	}

	public function testClassExists()
	{
		$this->assertTrue(class_exists(Resource::class));
	}

	public function testCanCreateInstanceOfClass()
	{
		$this->assertInstanceOf(Resource::class, $this->resourceObject);
	}

	public function testCanAccessValidAttribute()
	{
		$this->assertTrue($this->resourceObject->guid == '68fbd6d3-3079-43d6-9313-e7fe89c464c5');
	}

	public function testAccessingInvalidExceptionThrowsException()
	{
		$this->expectException(AttributeDoesNotExistException::class);

		$this->resourceObject->abc;
	}

	public function testCanSetAttribute()
	{
		$randomName = uniqid();

		$this->resourceObject->test = $randomName;

		$this->assertEquals($this->resourceObject->test, $randomName);
	}

	public function testCanUpdateResource()
	{
		$resource = $this->createResource();

		$randomName = uniqid();

		$resource->update([ 'name' => $randomName ]);

		$request = new Request($this->resource);
		$resource = $request->find($resource->id);

		$this->assertEquals($resource->name, $randomName);
	}

	public function testCanDeleteResource()
	{
		$resource = $this->createResource();

		$resource->delete();

		$this->expectException(ResourceNotFoundException::class);

		$request = new Request($this->resource);
		$request->find($resource->id);
	}

	private function createResource()
	{
		$request = new Request($this->resource);

		$resource = $request->create([
			'name' => 'Centrum test website',
			'url' => 'centrum.bluestorm.design'
		]);

		return $resource;
	}
}
