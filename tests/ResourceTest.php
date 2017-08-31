<?php

use Bluestorm\Centrum\Resource;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{

	protected $exampleJSON = "[
                                  {
                                    \"_id\": \"599fe2b1433aca29a1a3f306\",
                                    \"index\": 0,
                                    \"guid\": \"68fbd6d3-3079-43d6-9313-e7fe89c464c5\",
                                    \"isActive\": false,
                                    \"balance\": \"$2,367.97\",
                                    \"picture\": \"http://placehold.it/32x32\",
                                    \"age\": 23,
                                    \"eyeColor\": \"green\",
                                    \"name\": {
                                      \"first\": \"Davidson\",
                                      \"last\": \"Zimmerman\"
                                    },
                                    \"company\": \"ROCKLOGIC\"
                                  },
                                  {
                                    \"_id\": \"599fe2b11efa1b630db23f12\",
                                    \"index\": 1,
                                    \"guid\": \"0b766122-da20-4759-83fb-8b1dda271385\",
                                    \"isActive\": true,
                                    \"balance\": \"$3,395.02\",
                                    \"picture\": \"http://placehold.it/32x32\",
                                    \"age\": 39,
                                    \"eyeColor\": \"blue\",
                                    \"name\": {
                                      \"first\": \"Taylor\",
                                      \"last\": \"Clayton\"
                                    },
                                    \"company\": \"NIXELT\"
                                  }
                               ]";

	public function testClassExists()
	{
		$this->assertTrue(class_exists(Resource::class));
	}

	public function testCanCreateInstanceOfClass()
	{
		$this->assertTrue(new Resource(json_decode($this->exampleJSON, true)) instanceof Resource);
	}

	public function testCanAccessAttributeOnClass()
	{
		$resource = new Resource(json_decode($this->exampleJSON, true)[0]);
		var_dump($resource->guid);
		$this->assertTrue(json_decode($this->exampleJSON, true)[0]['guid'] == $resource->guid);
	}
}
