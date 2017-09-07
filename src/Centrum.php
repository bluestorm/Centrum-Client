<?php

namespace Bluestorm\Centrum;

use Bluestorm\Centrum\Exceptions\ApiKeyRequiredException;
use Bluestorm\Centrum\Exceptions\ApiUnavailableException;
use Bluestorm\Centrum\Exceptions\BaseUrlRequiredException;
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
	private static $availableResources = [];

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

		self::checkConfig('apiKey');
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

		self::checkConfig('baseUrl');
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
	 * @return bool
	 */
	public static function setDebug($debug)
	{
		self::$debug = $debug;
	}

	/**
	 * @param bool $force
	 * @return bool
	 */
	public static function isAvailable($force = false)
	{
		self::checkAvailability($force);

		return self::$available;
	}

	/**
	 * @param bool $force
	 * @return bool
	 * @throws ApiUnavailableException
	 */
	private static function checkAvailability($force = false)
	{
		if(empty(self::$availableResources) || $force)
		{
			try
			{
				self::getResources($force);

				self::$available = true;
			}
			catch(ApiUnavailableException $e)
			{
				self::$available = false;

				throw $e;
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

		if(!in_array($resource, self::$availableResources))
		{
			throw new ResourceNotValidException($resource);
		}
	}

	/**
	 * @param bool $force
	 * @return array
	 */
	public static function getResources($force = false)
	{
		if(empty(self::$availableResources) || $force)
		{
			$response = new Request('resource');
			$resources = $response->get();

			self::$availableResources = array_map(function($resource)
			{
				return $resource->resource;
			}, $resources->get());
		}

		return self::$availableResources;
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
	 * @param $property
	 * @throws ApiKeyRequiredException
	 * @throws BaseUrlRequiredException
	 */
	private static function checkConfig($property = null)
	{
		if($property === 'apiKey' || $property === null)
		{
			if(empty(self::$apiKey) || !self::$apiKey || strlen(self::$apiKey) < 1)
			{
				throw new ApiKeyRequiredException;
			}
		}

		if($property === 'baseUrl' || $property === null)
		{
			if(empty(self::$baseUrl) || !self::$baseUrl || strlen(self::$baseUrl) < 1)
			{
				throw new BaseUrlRequiredException;
			}
		}
	}
}
