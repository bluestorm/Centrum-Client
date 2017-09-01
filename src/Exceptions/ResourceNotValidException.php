<?php

namespace Bluestorm\Centrum\Exceptions;

class ResourceNotValidException extends \Exception
{
	public function __construct($resource)
	{
		parent::__construct('Resource ' . $resource . ' is not available from API.');
	}
}