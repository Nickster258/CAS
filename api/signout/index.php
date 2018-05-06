<?php

define('IN_CAS', true);

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
	if (!isset($input->username) || !isset($input->password)) {
		new ErrorResponse(
			400,
			"Invalid Parameters",
			"Username or password not set",
			null
		);
	}
	$username = $input->username;
	$password = $input->password;
	$valid_email = is_valid_email($username);
	if ($valid_email && (strlen($password) > 7)) {
		$uid = $handler->fetchUidFromEmail($valid_email);
		$hash = $handler->fetchHashFromUid($uid);
		if (password_verify($password, $hash)) {
			$handler->removeApiTokens($uid);
			new ApiResponse([],204);
		} else {
			new ErrorResponse(
				400,
				"Invalid Credentials",
				"Username or password do not match",
				null
			);
		}
	} else {
		new ErrorResponse(
			400,
			"Invalid Credentials",
			"There is an issue with the email formatting",
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
