<?php

use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Exceptions\ValidationErrorsException;
use Bluestorm\Centrum\Request;
use Bluestorm\Centrum\Response;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
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

	public function testClassExists()
	{
		$this->assertTrue(class_exists(Request::class));
	}

	public function testCanCreateInstanceOfClass()
	{
		$this->assertTrue(new Request($this->resource) instanceof Request);
	}

	public function testGetMethodReturnsResponse()
	{
		$request = new Request($this->resource);
		$this->assertTrue($request->get() instanceof Response);
	}

	public function testFindMethodReturnsResponse()
	{
		$request = new Request($this->resource);
		$resource = $request->find(30);
		$this->assertTrue($resource instanceof Response);
		// var_dump($resource);
	}

	public function testCreateReturnsResponse()
	{
		$request = Centrum::website()->create([
			'name' => 'Centrum test wesbsite',
			'url'  => 'centrum.bluestorm.design',
		]);
		$this->assertTrue($request instanceof Response);
	}

	public function testCreateReturnsValidationError()
	{
		$this->expectException(ValidationErrorsException::class);

		$request = Centrum::website()->create([
			'name' => 'Centrum test wesbsite',
		]);
	}

	public function testCanUpdateResource() {
		$randomName = uniqid();

		$request = Centrum::website()->update(385, [
			'name'	=>	$randomName,
		]);

		$this->assertTrue($request instanceof Response);

		$resource = Centrum::website()->find(385)->get();

		$this->assertTrue($resource->name == $randomName);

	}


	/*	TODO
		Update
		Delete
	*/

}
