<?php

use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Exceptions\ApiKeyRequiredException;
use Bluestorm\Centrum\Exceptions\ApiUnavailableException;
use Bluestorm\Centrum\Exceptions\BaseUrlRequiredException;
use Bluestorm\Centrum\Exceptions\ClassNotInstantiableException;
use Bluestorm\Centrum\Exceptions\ResourceNotValidException;
use Bluestorm\Centrum\Request;
use PHPUnit\Framework\TestCase;

class CentrumTest extends TestCase
{
	protected $apiKey;
	protected $baseUrl;

	public function setUp()
	{
		$this->apiKey = getenv('API_KEY');
		$this->baseUrl = getenv('BASE_URL');

		Centrum::setApiKey($this->apiKey);
		Centrum::setBaseUrl($this->baseUrl);

		if(!Centrum::isAvailable(true))
		{
			$this->fail('API must be available to run tests.');
		}
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
	}

	public function testRequiresBaseUrl()
	{
		$this->expectException(BaseUrlRequiredException::class);

		Centrum::setBaseUrl('');
	}

	public function testCanSetApiKey()
	{
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

	public function testCanCheckAvailability()
	{
		$this->assertTrue(Centrum::isAvailable(true));
	}

	public function testIsAvailableReturnsFalseWhenApiUnavailable()
	{
		Centrum::setBaseUrl('http://bad-base-url2.com/api/');

		$this->expectException(ApiUnavailableException::class);

		$available = Centrum::isAvailable(true);

		$this->assertFalse($available);
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
