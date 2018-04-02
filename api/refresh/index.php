<?php

define('IN_CAS', true);

require_once '../../includes/utilities.php';
require_once '../../includes/database.php';
require_once '../../includes/api_response.php';

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
	if (!isset($input->accessToken)) {
		new ErrorResponse(
			400,
			"Invalid Parameters",
			"AccessToken not found",
			null
		);
	}
	$access_token = $input->accessToken;
	$client_token = $input->clientToken;
	$client_token = $input->clientToken;
	$request_user = $input->requestUser;
	$details = $handler->apiGetAccessDetails($access_token);
	if($details) {
		if (strcmp($client_token, $details[0]['client_token']) === 0) {
			$access_token = get_unique_access_token($handler);
			$handler->apiUpdateAccessToken($access_token, $client_token);
			new ApiResponse([],204);
		} else {
			new ErrorResponse(
				400,
				"Invalid Parameters",
				"ClientToken does not match with the associated accessToken.",
				null
			);
		}
	} else {
		new ErrorResponse(
			400,
			"Invalid Access Token",
			"The accessToken provided is not associated with any account.",
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
