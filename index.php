<?php

declare(strict_types=1);

namespace iriki;

//the plan is to have a http stack (based on Slim for now)
//and scarf classes to route this general purpose stack
//to the specific Iriki version

session_start();

require_once(__DIR__ . '/vendor/autoload.php');

require_once(__DIR__ . '/scarf.php');

$s = new scarf();
//scarf::begin($s, []); 

//request
//expected default response
//options that'll affect operations
/*$do_something = function (object $request, array $response, array $options)
{
	$response['data'] = array(
		'key' => 'Value can be anything you wish.',
		'request' => $request->_state['request']
	);
	//we shall obey this code for the response
	//$response['code'] = 200;
	$response['message'] = 'This is an iriki.cloud endpoint.';


	return $response;
};*/

scarf::tie($s,
	[
		'uri_format' => '/{version}/{model}/{route}[/{url_params:.*}]'
	],
	scarf::iriki
);

?>
