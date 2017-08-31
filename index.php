<?php

use Bluestorm\Centrum\Centrum;

require_once 'vendor/autoload.php';

$apiKey = '6627b354a4cd3f828982d1a8168cd3201afeca1c';
$endPoint = 'https://f484e533.ngrok.io/api/';
Centrum::setEndpoint($endPoint);
Centrum::setApiKey($apiKey);

var_dump(Centrum::website()->update(385, [
	'name'	=>	'UPDATED',
	// 'url'	=>	'centrum.bluestorm.design'
]));