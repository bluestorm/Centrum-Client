<?php

use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Exceptions\ResourceNotFoundException;
use Bluestorm\Centrum\Request;
use Bluestorm\Centrum\Resource;
use Bluestorm\Centrum\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
	protected $resource = 'website';

	protected $apiKey;
	protected $baseUrl;

	public function setUp()
	{
		$this->apiKey = getenv('API_KEY');
		$this->baseUrl = getenv('BASE_URL');

		if(!$this->apiKey)
		{
			$this->fail('API key is required to run tests.');
		}

		if(!Centrum::isAvailable())
		{
			$this->fail('API must be available to run tests.');
		}

		Centrum::setApiKey($this->apiKey);
		Centrum::setBaseUrl($this->baseUrl);
	}

	public function testCanInstantiateClass()
	{
		$request = new Request($this->resource);
		$response = $request->get(2);

		$this->assertInstanceOf(Response::class, $response);
	}

	public function testCanAccessResources()
	{
		$request = new Request($this->resource);
		$response = $request->get(2);

		$resources = $response->get();

		$this->assertTrue(is_array($resources));
	}

	public function testCanResourceArrayContainsResources()
	{
		$request = new Request($this->resource);
		$response = $request->get(2);

		$resources = $response->get();

		$this->assertContainsOnlyInstancesOf(
			Resource::class,
			$resources
		);
	}

	public function testCanGetNextPageNumber()
	{
		$request = new Request($this->resource);
		$response = $request->get(2);

		$this->assertTrue(is_string($response->getNextPage()));
	}

	public function testCanGetPreviousPageNumber()
	{
		$request = new Request($this->resource);
		$response = $request->get(2);

		$this->assertTrue(is_string($response->getPreviousPage()));
	}

	public function testCanIterateResponse()
	{
		$request = new Request($this->resource);
		$response = $request->get(2);

		$this->assertInstanceOf(Traversable::class, $response);

		foreach($response as $resource)
		{
			// Nothing
		}
	}

	public function testEmptyResponseThrowsException()
	{
		$this->expectException(ResourceNotFoundException::class);

		(new Response('test', []))->first();
	}

	public function testGetNullNextPage()
	{
		$this->assertEquals((new Response('test', []))->getNextPage(), null);
	}

	public function testGetNullPreviousPage()
	{
		$this->assertEquals((new Response('test', []))->getPreviousPage(), null);
	}
}
