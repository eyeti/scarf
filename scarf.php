<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

class scarf
{
	//the url structure this expects
	private const uri_format = '/{version}/{model}/{route}[/{url_params:.*}]';

	//internal http library object instance
	//SlimPHP in this case
	private $_http = null;

	private $_state = array();

	//the scarf tie is a cycle: a http request, parsed, a system of nouns and verbs to match the parsed
	//and then the appropriate function to call and a valid response
	public static function tie(object &$instance, array $options = array(), $scarf_function)
	{
		//intercept some routes esp for CORS, debug, documentation etc
		//OPTIONS

		//this is a list of scarfs: models and their routes

		//do some pagination and archetype checks

		/*return array(
			'code' => 200,
			'data' => $options['body'] //'This is some random data'
		);*/

		if (isset($options['uri_format']))
		{
			$uri_format = $options['uri_format'];
		}
		else
		{
			$uri_format = Self::uri_format;
		}

		if (is_null($instance->_http))
		{
			$instance->_http = AppFactory::create();
		}

		if (!is_null($instance->_http))
		{
			$instance->_http->any($uri_format, function(Request $request, Response $response, $args) use ($options, $scarf_function) {
				$instance->_state = array(
					'request' => array(
						'ip' => $_SERVER['REMOTE_ADDR'],
						'method' => $request->getMethod(),
						'url' => $request->getUri(),
						'args' => $args,
						'headers' => $request->getHeaders(),

						'body' => array(
							'form' => $request->getParsedBody(),
							'json' => json_decode($request->getBody(), true),
							'files' => $request->getUploadedFiles()
						),
					),
					'options' => $options,
					'response' => null
				);

				//this is the entry point to other code
				//we parse on the scarf instance and other options

				//ideally, scarf_response should hold some data
				
				$instance->_state['response'] = $scarf_function(
					$instance->_state,
					array(
						'start' => gettimeofday(true),
						//we shall obey this code for the response
						'code' => 404,
						//truthy value that even the very dump can use to know if something is wrong
						'state' => false,
						//hold over from previous days
						//ideally we should drop to what? An apache like default page?
						'message' => 'This is an Iriki endpoint.',
						//this will be the one mainly filled
						'data' => array()
					)
				);

				$instance->_state['response']['end'] = gettimeofday(true);

				//this is the easy write
				//one may want to do an xml, json or other write

				$response = $response->withStatus(207);

				$body = $response->getBody();

				$body->write(json_encode($instance->_state['response']));

				return $response;
			});

			$instance->_http->run();
		}

		return true;
	}
}

?>
