<?php

declare(strict_types=1);

require_once(__DIR__ . '/vendor/autoload.php');

require_once(__DIR__ . '/src/scarf.php');

$s = new \eyeti\scarf\scarf();

//our scarf will call this function
$tie_function = function (array $request, array $response)
{	
	//you might begin by clearing $response too
	//$response = array();

	//this will change the response code from default
	//$response['code'] = 401;

	//write whatever it is to the data
	/*$response['data'] = array(
		'key' => 'Value can be anything you wish.'
	);
	$response['end'] = gettimeofday(true);*/

	//here, we have taken the liberty of inserting the original request

	$response = $request;

	return $response;
};

\eyeti\scarf\scarf::tie($s,
	[],
	$tie_function
);

?>
