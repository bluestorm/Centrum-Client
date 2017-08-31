<?php

namespace Bluestorm\Centrum;

use ArrayIterator;
use Bluestorm\Centrum\Exceptions\AttributeDoesNotExistException;
use IteratorAggregate;
use function GuzzleHttp\Psr7\parse_query;

class Response implements IteratorAggregate
{
	private $resources = [];
	private $meta = [];

	public function __construct($response)
	{
		$responseArray = json_decode((string)$response->getBody());
		$this->resources = $this->setResources($responseArray);
		$this->meta = $this->setMeta($responseArray);
	}

	private function setResources($response)
	{
		if ( !isset($response->data) )
		{
			return [];
		}

		$data = $response->data;

		if ( is_array($data) && count($data > 1))
		{
			$resources = array_map(function ($resource)
			{
				return new Resource($resource);
			}, $data);
		}
		else
		{
			$resources = new Resource($data);
		}

		return $resources;
	}

	private function setMeta($response)
	{
		return isset($response->meta) ? $response->meta : [];
	}

	public function getIterator()
	{
		return new ArrayIterator($this->resources);
	}

	public function get()
	{
		return $this->resources;
	}

	public function getMeta($key = null)
	{
		if ( is_null($key) )
		{
			return $this->meta;
		}
		else
		{
			if ( isset($this->meta[$key]) )
			{
				return $this->meta[$key];
			}
			else
			{
				throw new AttributeDoesNotExistException();

				return null;
			}
		}

		return $this->meta;
	}

	public function getNextPage()
	{
		if ( !isset($this->getPagination('links')->next) )
		{
			return null;
		}
		$parsed_url = parse_url($this->getPagination('links')->next);
		$parsed_query = parse_query($parsed_url['query']);

		return $parsed_query['page'];
	}

	public function getPagination($key = null)
	{
		if ( is_null($key) )
		{
			return $this->meta->pagination;
		}
		else
		{
			if ( isset($this->meta->pagination->{$key}) )
			{
				return $this->meta->pagination->{$key};
			}
			else
			{
				throw new AttributeDoesNotExistException();
			}
		}

		return $this->meta->pagination;
	}

	public function getPrevPage()
	{
		if ( !isset($this->getPagination('links')->previous) )
		{
			return null;
		}
		$parsed_url = parse_url($this->getPagination('links')->previous);
		$parsed_query = parse_query($parsed_url['query']);

		return $parsed_query['page'];
	}


}