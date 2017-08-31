<?php

namespace Bluestorm\Centrum;

use Bluestorm\Centrum\Exceptions\ApiKeyRequiredException;
use Bluestorm\Centrum\Exceptions\ClassNotInstantiableException;
use Bluestorm\Centrum\Exceptions\EndpointRequiredException;

/**
 * Class Centrum
 * @package Bluestorm\Centrum
 */
class Centrum
{

	/**
	 * @var
	 */
	private static $apiKey;
	/**
	 * @var
	 */
	private static $endPoint;
	/**
	 * @var bool
	 */
	private static $debug = false;
	/**
	 * @var
	 */
	private static $resources;

	/**
	 * Centrum constructor.
	 * @throws ClassNotInstantiableException
	 */
	public function __construct()
	{
		throw new ClassNotInstantiableException();
	}

	/**
	 * @return mixed
	 */
	public static function getApiKey()
	{
		return self::$apiKey;
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function setApiKey($key)
	{
		self::$apiKey = $key;

		return self::$apiKey;
	}

	/**
	 * @return mixed
	 */
	public static function getEndpoint()
	{
		return self::$endPoint;
	}

	/**
	 * @param $endPoint
	 *
	 * @return mixed
	 */
	public static function setEndpoint($endPoint)
	{
		self::$endPoint = $endPoint;

		return self::$endPoint;
	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return Request
	 */
	public static function __callStatic($name, $arguments)
	{
		self::checkConfig();

		return new Request($name);
	}

	/**
	 * @return void
	 */
	public static function checkConfig()
	{
		self::checkApiKey();
		self::checkEndPoint();
	}

	/**
	 * @throws ApiKeyRequiredException
	 */
	private static function checkApiKey()
	{
		if ( empty(self::$apiKey) )
		{
			throw new ApiKeyRequiredException();
		}
	}

	/**
	 * @throws EndpointRequiredException
	 */
	private static function checkEndPoint()
	{
		if ( empty(self::$endPoint) )
		{
			throw new EndpointRequiredException();
		}
	}

	/**
	 * @return bool
	 */
	public static function getDebug()
	{
		return self::$debug;
	}

	/**
	 * @param $debug
	 *
	 * @return bool
	 */
	public static function setDebug($debug)
	{
		self::$debug = $debug;

		return self::$debug;
	}

	/**
	 * @return Response
	 */
	public static function getResources()
	{
		if ( !empty(self::$resources) )
		{
			return self::$resources;
		}

		$response = new Request('resource');
		$resources = $response->get();
		self::$resources = $resources;

		return $resources;
	}
}