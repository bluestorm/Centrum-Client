<?php

namespace Bluestorm\Centrum;

use ArrayIterator;
use Bluestorm\Centrum\Exceptions\ResourceNotFoundException;
use IteratorAggregate;
use function GuzzleHttp\Psr7\parse_query;

class Response implements IteratorAggregate
{
	private $resource;
	private $resources = [];
	private $meta = [];

	/**
	 * Response constructor.
	 * @param $resource
	 * @param $response
	 */
	public function __construct($resource, $response)
	{
		$responseArray = is_array($response) ? $response : json_decode((string) $response->getBody());

		$this->resource = $resource;

		$this->setResources($responseArray);
		$this->setMeta($responseArray);
	}

	/**
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->resources);
	}

	/**
	 * @param $response
	 * @return $this
	 */
	private function setResources($response)
	{
		if(isset($response->data))
		{
			if(is_array($response->data))
			{
				$resources = array_map(function($resourceData)
				{
					return new Resource($this->resource, $resourceData);
				}, $response->data);
			}
			else
			{
				$resources = [ new Resource($this->resource, $response->data) ];
			}

			$this->resources = $resources;
		}

		return $this;
	}

	/**
	 * @param $response
	 * @return $this
	 */
	private function setMeta($response)
	{
		$this->meta = isset($response->meta) ? $response->meta : [];

		return $this;
	}

	/**
	 * @return null
	 */
	public function getPagination()
	{
		return isset($this->meta->pagination->links) ? $this->meta->pagination->links : null;
	}

	/**
	 * @return null
	 */
	public function getNextPage()
	{
		if(!isset($this->getPagination()->next))
		{
			return null;
		}

		$parsed_url = parse_url($this->getPagination()->next);
		$parsed_query = parse_query($parsed_url['query']);

		return $parsed_query['page'];
	}

	/**
	 * @return null
	 */
	public function getPreviousPage()
	{
		if(!isset($this->getPagination()->previous))
		{
			return null;
		}

		$parsed_url = parse_url($this->getPagination()->previous);
		$parsed_query = parse_query($parsed_url['query']);

		return $parsed_query['page'];
	}

	/**
	 * @return array
	 */
	public function get()
	{
		return $this->resources;
	}

	/**
	 * @return mixed
	 * @throws ResourceNotFoundException
	 */
	public function first()
	{
		if(!isset($this->resources[0]))
		{
			throw new ResourceNotFoundException;
		}

		return $this->resources[0];
	}
}