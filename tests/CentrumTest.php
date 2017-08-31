<?php

use Bluestorm\Centrum\Centrum;
use Bluestorm\Centrum\Exceptions\ClassNotInstantiableException;
use Bluestorm\Centrum\Request;
use PHPUnit\Framework\TestCase;

class CentrumTest extends TestCase
{
	protected $apiKey = '6627b354a4cd3f828982d1a8168cd3201afeca1c';
	protected $endPoint = 'https://f484e533.ngrok.io/api/';

	public function testClassExists()
	{
		$this->assertTrue(class_exists(Centrum::class));
	}

	public function testCantCreateInstanceOfClass()
	{
		$this->expectException(ClassNotInstantiableException::class);
		new Centrum();
	}

	public function testCanSetApiKey()
	{
		$this->assertEquals($this->apiKey, Centrum::setApiKey($this->apiKey));
	}

	public function testCanSetEndPoint()
	{
		$this->assertEquals($this->endPoint, Centrum::setEndPoint($this->endPoint));
	}

	public function testStaticFunctionReturnsResourceRequest()
	{
		Centrum::setApiKey($this->apiKey);
		Centrum::setEndpoint($this->endPoint);
		$this->assertTrue(Centrum::website() instanceof Request);
	}

}