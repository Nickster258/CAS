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
	$token = false;
	if (isset($input->cookie)) {
		$token = $input->cookie;
	}
	if (strlen($token) == 64) {
		if ($uid = $handler->fetchUidFromToken($token)) {
			$user = $handler->fetchDetailsFromUid($uid);
			$uuid = $user[0]['uuid'];
			$m_uuid = $user[0]['m_uuid'];
			$name = $user[0]['username'];
			$email = $user[0]['email'];
			new ApiResponse([
				"user" => [
					"uuid" => $uid,
					"m_uuid" => $m_uuid,
					"name" => $name,
					"email" => $email
				]
			], 200);
		} else {
			new ErrorResponse(
				400,
				"Invalid Cookie",
				"The cookie is either incorrect or no longer valid",
				null
			);
		}
	} else {
		new ErrorResponse(
			400,
			"Invalid Cookie Parameter",
			"There was an error with the cookie field.",
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
