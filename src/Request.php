<?php

namespace Bluestorm\Centrum;

use Bluestorm\Centrum\Exceptions\IdIsRequiredException;
use Bluestorm\Centrum\Exceptions\IdMustBeIntegerException;
use Bluestorm\Centrum\Exceptions\ResourceIsRequiredException;
use Bluestorm\Centrum\Exceptions\ValidationErrorsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Request
{

	protected $client;
	protected $resource;
	protected $headers = [];
	protected $data = [];

	public function __construct($resource)
	{
		$this->client = new Client([
			'base_uri' => Centrum::getEndpoint(),
		]);

		if ( empty($resource) )
		{
			throw new ResourceIsRequiredException();
		}

		$this->resource = $resource;
		$this->headers['X-Authorization'] = Centrum::getApiKey();
	}

	public function get($page = 1)
	{
		$response = $this->client->get($this->resource, ['headers' => $this->headers, 'query' => ['page' => $page], 'debug' => Centrum::getDebug()]);

		return new Response($response);
	}

	public function find($id)
	{
		$this->checkID($id);
		$path = $this->resource;
		$path .= '/' . $id;

		$response = $this->client->get($path, ['headers' => $this->headers]);

		return new Response($response);
	}

	protected function checkID($id)
	{

		if ( is_null($id) )
		{
			throw new IdIsRequiredException();
		}

		if ( !is_int($id) )
		{
			throw new IdMustBeIntegerException();
		}

	}

	/**
	 * @param $data
	 *
	 * @return Response
	 * @throws ValidationErrorsException
	 */
	public function create($data)
	{

		try
		{
			$response = $this->client->post($this->resource, ['headers' => $this->headers, 'form_params' => $data]);

			return new Response($response);
		} catch (ClientException $e)
		{
			//Error messages from API
			$response = $e->getResponse();
			throw new ValidationErrorsException($response);

			return new Response($response);
		}
	}

	public function update($id = null, $data = [])
	{
		$this->checkID($id);
		$path = $this->resource;
		$path .= '/' . $id;
		$response = $this->client->put($path, ['headers' => $this->headers, 'form_params' => $data]);

		return new Response($response);
	}

	public function delete($id = null)
	{
		$this->checkID($id);
		$path = $this->resource;
		$path .= '/' . $id;
		$response = $this->client->delete($path, ['headers' => $this->headers]);

		return new Response($response);
	}

}