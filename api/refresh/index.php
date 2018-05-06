<?php

require_once 'includes/constants.php';
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
	$details = $handler->apiGetAccessDetails($access_token);
	if($details) {
		if (strcmp($client_token, $details['client_token']) === 0) {
			$user_details = $handler->fetchDetailsFromUid($details['uid']);
			$group_details = $handler->fetchGroupDetails($user_details['group_level']);
			$access_token = get_unique_access_token($handler);
			$handler->apiUpdateAccessToken($access_token, $client_token);
			new ApiResponse([
				"accessToken" => $access_token,
				"clientToken" => $client_token,
				"user" => [
					"uuid" => $user_details['uid'],
					"m_uuid" => $user_details['m_uuid'],
					"name" => $user_details['username']
				],
				"group" => [
					"level" => $group_details['group_level'],
					"name" => $group_details['group_name'],
					"permissions" => [
						"ingame" => $group_details['level_ingame'],
						"irc" => $group_details['level_irc'],
						"logs" => boolval($group_details['level_logs'])
					]
				]
			],200);
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
