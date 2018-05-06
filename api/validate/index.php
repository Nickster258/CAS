<?php

define('IN_CAS', true);
require_once '../../includes/constants.php';
require_once ROOT_DIR . 'includes/utilities.php';
require_once ROOT_DIR . 'includes/database.php';
require_once ROOT_DIR . 'includes/api_response.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

if (strcmp(filter_input(INPUT_SERVER, 'REQUEST_METHOD'),'POST') != 0) {
	new ErrorResponse(
		400,
		"Unsupported Media Type",
		"The server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method",
		null
	);
} elseif ($input = valid_json('php://input')) {
	if(!isset($input->accessToken)) {
		new ErrorResponse(
			400,
			"Invalid Access Token",
			"No access token was provided",
			null
		);
	}
	$access_token = $input->accessToken;
	$client_token = false;
	if(isset($input->clientToken)) {
		$client_token = $input->clientToken;
	}
	$details = $handler->apiGetAccessDetails($access_token);
	if(($client_token == $details['client_token']) && $details) {
		new ApiResponse([], 204);
	} else {
		new ErrorResponse(
			400,
			"Invalid Parameters",
			"Either the access token or the client token is not valid",
			null
		);
	}
} else {
	new ErrorResponse(
		400,
		"Invalid Post Formatting",
		"The post data was formatted incorrectly",
		null
	);
}

?>
