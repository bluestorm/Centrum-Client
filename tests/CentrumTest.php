<?php

use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Exceptions\ApiKeyRequiredException;
use Bluestorm\Centrum\Exceptions\ApiUnavailableException;
use Bluestorm\Centrum\Exceptions\ClassNotInstantiableException;
use Bluestorm\Centrum\Exceptions\ResourceNotValidException;
use Bluestorm\Centrum\Request;
use PHPUnit\Framework\TestCase;

class CentrumTest extends TestCase
{
	protected $apiKey = '6627b354a4cd3f828982d1a8168cd3201afeca1c';
	protected $baseUrl = 'http://centrum.dev/api/';

	public function setUp()
	{
		if(!Centrum::isAvailable())
		{
			$this->fail('API must be available to run tests.');
		}
	}

	public function testClassExists()
	{
		$this->assertTrue(class_exists(Centrum::class));
	}

	public function testCantCreateInstanceOfClass()
	{
		$this->expectException(ClassNotInstantiableException::class);

		new Centrum();
	}

	public function testRequiresApiKey()
	{
		$this->expectException(ApiKeyRequiredException::class);

		Centrum::setApiKey('');
		Centrum::getResources();
	}

	public function testCanSetApiKey()
	{
		Centrum::setApiKey($this->apiKey);

		$this->assertEquals($this->apiKey, Centrum::getApiKey());
	}

	public function testCanSetBaseUrl()
	{
		Centrum::setBaseUrl($this->baseUrl);

		$this->assertEquals($this->baseUrl, Centrum::getBaseUrl());
	}

	public function testCanSetDebug()
	{
		Centrum::setDebug(true);

		$this->assertEquals(Centrum::getDebug(), true);

		Centrum::setDebug(false); // Undo that
	}

	public function testCanGetResources()
	{
		$resources = Centrum::getResources();

		$this->assertTrue(count($resources) > 0);
	}

	public function testStaticResourceMethod()
	{
		$this->assertInstanceOf(Request::class, Centrum::website());
	}

	public function testInvalidResourceThrowsException()
	{
		$this->expectException(ResourceNotValidException::class);

		Centrum::resource('fake');
	}
}
