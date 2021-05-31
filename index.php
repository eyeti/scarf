<?php

declare(strict_types=1);

require_once(__DIR__ . '/vendor/autoload.php');

require_once(__DIR__ . '/scarf.php');

$s = new scarf();

//our scarf will call this function
$tie_function = function (array $state, array $default)
{
	$response = array();
	
	//we shall obey this code for the response
	$response['code'] = 200;

	$response['data'] = array(
		'key' => 'Value can be anything you wish.',
		'state' => $state
	);

	//$response['message'] = 'This is an iriki.cloud endpoint.';

	return $response;
};

scarf::tie($s,
	[
		'uri_format' => '/{version}/{model}/{route}[/{url_params:.*}]'
	],
	$tie_function
);

?>
