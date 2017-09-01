<?php

namespace Bluestorm\Centrum\Exceptions;

class ValidationErrorsException extends \Exception
{
	public $errors = [];

	public function __construct($errors = [])
	{
		$this->errors = $errors;
	}
}