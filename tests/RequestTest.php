<?php

use Bluestorm\Centrum\ApiUnavailableException;
use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Exceptions\AuthenticationException;
use Bluestorm\Centrum\Exceptions\IdIsRequiredException;
use Bluestorm\Centrum\Exceptions\IdMustBeIntegerException;
use Bluestorm\Centrum\Exceptions\ResourceIsRequiredException;
use Bluestorm\Centrum\Exceptions\ValidationErrorsException;
use Bluestorm\Centrum\Request;
use Bluestorm\Centrum\Resource;
use Bluestorm\Centrum\ResourceNotFoundException;
use Bluestorm\Centrum\Response;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
	protected $resource = 'website';
	protected $apiKey = '6627b354a4cd3f828982d1a8168cd3201afeca1c';
	protected $baseUrl = 'http://centrum.dev/api/';

	public function setUp()
	{
		Centrum::setApiKey($this->apiKey);
		Centrum::setBaseUrl($this->baseUrl);
	}

	public function testClassExists()
	{
		$this->assertTrue(class_exists(Request::class));
	}

	public function testBadApiKeyThrowsException()
	{
		$this->expectException(AuthenticationException::class);

		Centrum::setApiKey('bad-key');
		Centrum::resource('website')->get();
	}

	public function testBadBaseUrlThrowsException()
	{
		$this->expectException(ApiUnavailableException::class);

		Centrum::setBaseUrl('http://bad-base-url.com/api/');
		Centrum::resource('website')->get();
	}

	public function testCantInstantiateRequestWithoutResourceName()
	{
		$this->expectException(ResourceIsRequiredException::class);

		new Request(null);
	}

	public function testIdIsRequired()
	{
		$this->expectException(IdIsRequiredException::class);

		$request = new Request('test');
		$request->find(null);
	}

	public function testIdMustBeInteger()
	{
		$this->expectException(IdMustBeIntegerException::class);

		$request = new Request('test');
		$request->find('abc');
	}

	public function testGetMethodReturnsResponse()
	{
		$request = new Request($this->resource);

		$this->assertInstanceOf(Response::class, $request->get());
	}

	public function testFindMethodReturnsResponse()
	{
		$request = new Request($this->resource);
		$resource = $request->find(30);

		$this->assertInstanceOf(Resource::class, $resource);
	}

	public function testInvalidResourceRequest()
	{
		$this->expectException(ResourceNotFoundException::class);

		$request = new Request($this->resource);
		$request->find(-1);
	}

	public function testCreateReturnsResource()
	{
		$resource = $this->createResource();

		$this->assertInstanceOf(Resource::class, $resource);
	}

	public function testCreateReturnsValidationError()
	{
		$this->expectException(ValidationErrorsException::class);

		$request = new Request($this->resource);
		$request->create([
			'name' => 'Centrum test website'
		]);
	}

	public function testCanUpdateResource()
	{
		$resource = $this->createResource();

		$randomName = uniqid();

		$request = new Request($this->resource);
		$request->update($resource->id, [
			'name' => $randomName
		]);

		$request = new Request($this->resource);
		$resource = $request->find($resource->id);

		$this->assertEquals($resource->name, $randomName);
	}

	public function testCanDeleteResource()
	{
		$resource = $this->createResource();

		$request = Centrum::resource($this->resource);
		$request->delete($resource->id);

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
