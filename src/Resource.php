<?php

namespace Bluestorm\Centrum;


use Bluestorm\Centrum\Exceptions\AttributeDoesNotExistException;

class Resource
{
	protected $resource;
	protected $attributes = [];

	public function __construct($resource, $resourceArray = [])
	{
		$this->resource = $resource;
		$this->attributes = (array) $resourceArray;
	}

	public function __set($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	public function __get($key)
	{
		if(!isset($this->attributes[$key]))
		{
			throw new AttributeDoesNotExistException($key . ' attribute does not exist on resource ' . $this->resource);
		}

		return $this->attributes[$key];
	}

	public function update($data)
	{
		$request = new Request($this->resource);

		return $request->update($this->id, $data);
	}

	public function delete()
	{
		$request = new Request($this->resource);

		return $request->delete($this->id);
	}
}