<?php

namespace Bluestorm\Centrum;

use Bluestorm\Centrum\Exceptions\AuthenticationException;
use Bluestorm\Centrum\Exceptions\ApiUnavailableException;
use Bluestorm\Centrum\Exceptions\IdIsRequiredException;
use Bluestorm\Centrum\Exceptions\IdMustBeIntegerException;
use Bluestorm\Centrum\Exceptions\ResourceIsRequiredException;
use Bluestorm\Centrum\Exceptions\ResourceNotFoundException;
use Bluestorm\Centrum\Exceptions\ResourceNotValidException;
use Bluestorm\Centrum\Exceptions\ValidationErrorsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class Request
{
	protected $client;
	protected $resource;
	protected $headers = [];
	protected $data = [];

	/**
	 * Request constructor.
	 * @param $resource
	 * @throws ResourceIsRequiredException
	 */
	public function __construct($resource)
	{
		$this->client = new Client([
			'base_uri' => Centrum::getBaseUrl(),
		]);

		if(empty($resource))
		{
			throw new ResourceIsRequiredException;
		}

		$this->resource = $resource;
		$this->headers['X-Authorization'] = Centrum::getApiKey();
	}

	/**
	 * @param $method
	 * @param $endpoint
	 * @param array $options
	 * @return Response
	 */
	private function call($method, $endpoint, $options = [])
	{
		try
		{
			$response = $this->client->request($method, $endpoint, array_merge([ 'headers' => $this->headers, 'debug' => Centrum::getDebug() ], $options));

			return new Response($this->resource, $response);
		}
		catch(ConnectException $e)
		{
			$this->handleException($e);
		}
		catch(ClientException $e)
		{
			$this->handleException($e);
		}
		catch(RequestException $e)
		{
			$this->handleException($e);
		}

		return null;
	}

	/**
	 * @param \Exception $e
	 * @throws ApiUnavailableException
	 * @throws AuthenticationException
	 * @throws ResourceNotFoundException
	 * @throws ResourceNotValidException
	 * @throws ValidationErrorsException
	 */
	private function handleException(\Exception $e)
	{
		if($e->getCode() == 410)
		{
			throw new ResourceNotValidException('Invalid resource: ' . $this->resource);
		}
		elseif($e->getCode() == 404)
		{
			throw new ResourceNotFoundException;
		}
		elseif($e->getCode() == 401)
		{
			throw new AuthenticationException;
		}
		elseif($e->getCode() == 400)
		{
			$errors = json_decode((string) $e->getResponse()->getBody(), true);

			throw new ValidationErrorsException($errors);
		}
		elseif($e instanceof RequestException)
		{
			throw new ApiUnavailableException('Request error: ' . $e->getMessage(), $e->getCode(), $e);
		}
		else
		{
			throw new ApiUnavailableException('Something went wrong!', $e->getCode(), $e);
		}
	}

	/**
	 * @param $id
	 * @throws IdIsRequiredException
	 * @throws IdMustBeIntegerException
	 */
	private function checkID($id)
	{
		if(is_null($id))
		{
			throw new IdIsRequiredException;
		}

		if(!is_int($id))
		{
			throw new IdMustBeIntegerException;
		}
	}

	/**
	 * @param int $page
	 * @return Response
	 */
	public function get($page = 1)
	{
		$response = $this->call('get', $this->resource, [ 'query' => [ 'page' => $page ] ]);

		return $response;
	}

	/**
	 * @param $id
	 * @return Response|Resource
	 */
	public function find($id)
	{
		$this->checkID($id);

		$response = $this->call('get', $this->resource . '/' . $id);

		return $response->first();
	}

	/**
	 * @param $data
	 * @return Resource
	 */
	public function create($data)
	{
		$response = $this->call('post', $this->resource, [ 'form_params' => $data ]);

		return $response->first();
	}

	/**
	 * @param null $id
	 * @param array $data
	 * @return Resource
	 */
	public function update($id = null, $data = [])
	{
		$this->checkID($id);

		$response = $this->call('put', $this->resource . '/' . $id, [ 'form_params' => $data ]);

		return $response->first();
	}

	/**
	 * @param null $id
	 * @return Resource
	 */
	public function delete($id = null)
	{
		$this->checkID($id);

		$response = $this->call('delete', $this->resource . '/' . $id);

		return $response->first();
	}
}
