<?php

namespace eyeti\scarf;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

class scarf
{
	//the url structure this expects
	private const URI_FORMAT = '/{version}/{noun}/{verb}[/{url_params:.*}]';
	private const DEFAULT_RESPONSE_CODE = 202;

	//internal http library object instance
	//SlimPHP in this case
	private $_http = null;

	//parsed request
	private $_request = array();
	private $_response = null;

	//the scarf tie is a cycle: a http request, parsing and a response function
	//the scarf function must have two array variables: request and response
	//request will hold the parsed http request (structure will be published)
	//response will be the beginning of the http response body
	//the tie function should modify the response array
	public static function tie(object &$instance, array $options = array(), $scarf_function)
	{
		if (isset($options['URI_FORMAT']))
		{
			$URI_FORMAT = $options['URI_FORMAT'];
		}
		else
		{
			$URI_FORMAT = Self::URI_FORMAT;
		}

		if (is_null($instance->_http))
		{
			$instance->_http = AppFactory::create();
		}

		if (!is_null($instance->_http))
		{
			$instance->_http->any($URI_FORMAT, function(Request $request, Response $response, $args) use ($options, $scarf_function) {
				$instance->_request = array(
					'ip' => $_SERVER['REMOTE_ADDR'],
					'method' => $request->getMethod(),
					'url' => $request->getUri(),
					'args' => $args,
					'headers' => $request->getHeaders(),

					'body' => array(
						'form' => $request->getParsedBody(),
						'json' => json_decode($request->getBody(), true),
						'files' => $request->getUploadedFiles()
					)
				);

				//this is the entry point to other code
				//we parse on the scarf instance and other options

				//ideally, scarf_response should hold some data
				
				$instance->_response = $scarf_function(
					$instance->_request,
					array(
						'start' => gettimeofday(true),
						//we shall obey this code for the response so change it
						'code' => Self::DEFAULT_RESPONSE_CODE,
						//truthy value that even the very dump can use to know if something is wrong
						'state' => false,
						//hold over from previous days
						//ideally we should drop to what? An apache like default page?
						'message' => 'This is has been tied by https://github.com/eyeti/scarf.',
						//this will be the one mainly filled
						'data' => array()
					)
				);

				//this can be inserted here to track how long the tie function took
				//$instance->_response['end'] = gettimeofday(true);

				//this is the easy write
				//one may want to do an xml, json or other write

				//response['code'] is used to set the response code
				//we might wish to drop it also
				//if it is not set, we shall use the default
				$response_code = Self::DEFAULT_RESPONSE_CODE;
				if (isset($instance->_response['code']))
				{
					$response_code = $instance->_response['code'];
					unset($instance->_response['code']);
				}
				
				$response = $response->withStatus($response_code);

				$body = $response->getBody();

				$body->write(json_encode($instance->_response));

				return $response;
			});

			$instance->_http->run();
		}

		return true;
	}
}

?>
