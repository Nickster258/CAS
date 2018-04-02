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
	$agent = $input->agent;
	$username = $input->username;
	$password = $input->password;
	$client_token = false;
	$request_user = false;
	if (isset($input->clientToken)) {
		$client_token = $input->clientToken;
	}
	if (isset($input->requestUser)) {
		$request_user = $input->requestUser;
	}

	$valid_email = is_valid_email($username);
	if ($valid_email && (strlen($password) > 7)) {
		$uid = $handler->fetchUidFromEmail($valid_email);
		$hash = $handler->fetchHashFromUid($uid);
		if (password_verify($password, $hash)) {
			if (!$handler->apiClientTokenExists($uid, $client_token)) {
				$access_token = get_unique_access_token($handler);
				$details = $handler->fetchDetailsFromUid($uid);
				$group_details = $handler->fetchGroupDetails($details['group_level']);
				$time = time();
				$handler->setAccessToken($access_token, $client_token, $uid, $time);
				new ApiResponse([
					"accessToken" => $access_token,
					"clientToken" => $client_token,
					"user" => [
						"uuid" => $uid,
						"m_uuid" => $details['m_uuid'],
						"name" => $details['username']
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
				], 200);
			} else {
				new ErrorResponse(
					400,
					"Client Token Error",
					"Client token already exists for that user.",
					null
				);
			}
		} else {
			new ErrorResponse(
				400,
				"ForbiddenOperationException",
				"Invalid credentials. Invalid username or password.",
				null
			);
		}
	} else {
		new ErrorResponse(
			400,
			"Invalid Email Formatting",
			"The email was formatted incorrectly",
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
