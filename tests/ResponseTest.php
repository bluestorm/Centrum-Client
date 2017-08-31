<?php

use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Resource;
use Bluestorm\Centrum\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
	protected $resource = 'website';
	protected $apiKey = '6627b354a4cd3f828982d1a8168cd3201afeca1c';
	protected $endPoint = 'https://f484e533.ngrok.io/api/';

	public function setUp()
	{
		parent::setUp();
		Centrum::setApiKey($this->apiKey);
		Centrum::setEndpoint($this->endPoint);
	}

	public function testCanInstantiateClass()
	{
		$response = $this->getResponse();
		$this->assertTrue($response instanceof Response);
	}

	protected function getResponse()
	{
		$response = Centrum::website()->get(2);

		return $response;
	}

	public function testCanAccessResources()
	{
		$response = $this->getResponse();
		$resources = $response->get();
		$this->assertTrue(is_array($resources));
	}

	public function testCanResourceArrayContainsResources()
	{
		$response = $this->getResponse();
		$resources = $response->get();
		$this->assertContainsOnlyInstancesOf(
			Resource::class,
			$resources
		);
	}

	public function testCanGetNextPageNumber()
	{
		$response = $this->getResponse();
		$this->assertTrue(is_string($response->getNextPage()));
	}

	public function testCanGetPreviousPageNumber()
	{
		$response = $this->getResponse();
		$this->assertTrue(is_string($response->getPrevPage()));
	}


}
