<?php

use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Request;
use Bluestorm\Centrum\Resource;
use Bluestorm\Centrum\ResourceNotFoundException;
use Bluestorm\Centrum\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
	protected $resource = 'website';
	protected $apiKey = '6627b354a4cd3f828982d1a8168cd3201afeca1c';
	protected $baseUrl = 'http://centrum.dev/api/';

	public function setUp()
	{
		Centrum::setApiKey($this->apiKey);
		Centrum::setBaseUrl($this->baseUrl);

		if(!Centrum::isAvailable())
		{
			$this->fail('API must be available to run tests.');
		}
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
