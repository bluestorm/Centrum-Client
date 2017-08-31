<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 31/08/2017
 * Time: 11:01
 */

namespace Bluestorm\Centrum\Exceptions;


class ValidationErrorsException extends \Exception
{

	public $errors = [];
	/**
	 * ValidationErrorsException constructor.
	 *
	 * @param null|\Psr\Http\Message\ResponseInterface $response
	 */
	public function __construct($response)
	{
		$response = json_decode((string)$response->getBody(), true);
		if ( !isset($response['errors']) )
		{
			return [];
		}

		$errors = $response['errors'];

		if ( is_array($errors) )
		{
			$this->errors = $errors;
		}
		else
		{
			$this->errors = [$errors];
		}
	}

}