<?php

declare(strict_types=1);

namespace eyeti\scarf;

require_once(__DIR__ . '/vendor/autoload.php');

require_once(__DIR__ . '/scarf.php');

$s = new scarf();

//our scarf will call this function
$tie_function = function (array $request, array $response)
{	
	//you might begin by clearing $response too
	$response = array();

	//this will change the response code from default
	//$response['code'] = 401;

	//write whatever it is to the data
	//here, we have taken the liberty of inserting the original request
	$response['data'] = array(
		'key' => 'Value can be anything you wish.',
		'request' => $request
	);

	//$response['end'] = gettimeofday(true);

	$response = $request;

	return $response;
};

scarf::tie($s,
	[],
	$tie_function
);

?>
