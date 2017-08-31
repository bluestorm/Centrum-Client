<?php

namespace Bluestorm\Centrum;


use Bluestorm\Centrum\Exceptions\AttributeDoesNotExistException;

class Resource
{
	protected $response;
	protected $attributes = [];

	public function __construct($resourceArray = [])
	{
		$this->attributes = $resourceArray;
	}

	public function __get($key)
	{
		if ( !isset($this->attributes[$key]) )
		{
			throw new AttributeDoesNotExistException;

			return null;
		}

		return $this->attributes[$key];
	}

}