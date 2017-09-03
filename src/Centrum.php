<?php

namespace Bluestorm\Centrum;

use Bluestorm\Centrum\Exceptions\ApiKeyRequiredException;
use Bluestorm\Centrum\Exceptions\ApiUnavailableException;
use Bluestorm\Centrum\Exceptions\ClassNotInstantiableException;
use Bluestorm\Centrum\Exceptions\ResourceNotValidException;

/**
 * Class Centrum
 * @package Bluestorm\Centrum
 */
class Centrum
{
	/**
	 * @var string $apiKey
	 */
	private static $apiKey;

	/**
	 * @var string $baseUrl
	 */
	private static $baseUrl = 'https://centrum.bluestorm.design/api/';

	/**
	 * @var bool $debug
	 */
	private static $debug = false;

	/**
	 * @var array $resources
	 */
	private static $resources = [];

	/**
	 * @var bool $available
	 */
	private static $available = true;

	/**
	 * Centrum constructor.
	 * @throws ClassNotInstantiableException
	 */
	public function __construct()
	{
		throw new ClassNotInstantiableException;
	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return Request
	 */
	public static function __callStatic($name, $arguments)
	{
		return self::resource($name);
	}

	/**
	 * @return string
	 */
	public static function getApiKey()
	{
		return self::$apiKey;
	}

	/**
	 * @param $key
	 *
	 * @return string
	 */
	public static function setApiKey($key)
	{
		self::$apiKey = $key;

		self::checkConfig();
	}

	/**
	 * @return string
	 */
	public static function getBaseUrl()
	{
		return self::$baseUrl;
	}

	/**
	 * @param $baseUrl
	 *
	 * @return string
	 */
	public static function setBaseUrl($baseUrl)
	{
		self::$baseUrl = $baseUrl;
	}

	/**
	 * @return bool
	 */
	public static function getDebug()
	{
		return self::$debug;
	}

	/**
	 * @param bool $debug
	 *
	 * @return bool
	 */
	public static function setDebug($debug)
	{
		self::$debug = $debug;
	}

	/**
	 * @return bool
	 */
	public static function isAvailable()
	{
		self::checkAvailability();

		return self::$available;
	}

	/**
	 * @return bool
	 * @throws ApiUnavailableException
	 */
	private static function checkAvailability()
	{
		if(empty(self::$resources))
		{
			try
			{
				self::getResources(false);

				self::$available = true;
			}
			catch(ApiUnavailableException $e)
			{
				// Allows pure 404 exceptions (not resource 410 not found exceptions) to
				// throw ApiUnavailableException instead. We catch it to

				self::$available = false;
			}
		}

		return self::$available;
	}

	/**
	 * @param $resource
	 * @throws ResourceNotValidException
	 */
	public static function checkResourceIsValid($resource)
	{
		self::getResources();

		if(!in_array($resource, self::$resources))
		{
			throw new ResourceNotValidException($resource);
		}
	}

	/**
	 * @param bool $checkAvailability
	 * @return array
	 */
	public static function getResources($checkAvailability = true)
	{
		if(empty(self::$resources))
		{
			if($checkAvailability)
			{
				self::checkAvailability();
			}

			$response = new Request('resource');
			$resources = $response->get();

			self::$resources = $resources->get();
		}

		return self::$resources;
	}

	/**
	 * @param $resource
	 * @return Request
	 */
	public static function resource($resource)
	{
		self::checkConfig();
		self::checkResourceIsValid($resource);

		return new Request($resource);
	}

	/**
	 * @throws ApiKeyRequiredException
	 */
	private static function checkConfig()
	{
		if(empty(self::$apiKey) || !self::$apiKey || strlen(self::$apiKey) < 1)
		{
			throw new ApiKeyRequiredException;
		}
	}
}
