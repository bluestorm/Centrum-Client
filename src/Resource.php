<?php

namespace Bluestorm\Centrum;


use Bluestorm\Centrum\Exceptions\AttributeDoesNotExistException;

class Resource
{
	protected $resource;
	protected $attributes = [];

	/**
	 * Resource constructor.
	 * @param $resource
	 * @param array $resourceArray
	 */
	public function __construct($resource, $resourceArray = [])
	{
		$this->resource = $resource;
		$this->attributes = (array) $resourceArray;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	/**
	 * @param $key
	 * @return mixed
	 * @throws AttributeDoesNotExistException
	 */
	public function __get($key)
	{
		if(!isset($this->attributes[$key]))
		{
			throw new AttributeDoesNotExistException($key . ' attribute does not exist on resource ' . $this->resource);
		}

		return $this->attributes[$key];
	}

	/**
	 * @param $data
	 * @return Resource
	 */
	public function update($data)
	{
		$request = new Request($this->resource);

		return $request->update($this->id, $data);
	}

	/**
	 * @return Resource
	 */
	public function delete()
	{
		$request = new Request($this->resource);

		return $request->delete($this->id);
	}
}