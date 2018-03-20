<?php

define('IN_CAS', true);

require_once '../../includes/utilities.php';
require_once '../../includes/database.php';
require_once '../../includes/api_response.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

if (empty($_POST)) {
	new ErrorResponse(
		400,
		"Unsupported Media Type",
		"The server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method",
		null
	);
} elseif (valid_json(preg_replace('/\s+/','',file_get_contents('php://input')))) {
	$input = json_decode(preg_replace('/\s+/','',file_get_contents('php://input')));
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
			$access_token = get_unique_access_token($handler);
			$handler->setAccessToken($access_token, $client_token, $uid);
			try {
			new GeneralResponse([
				"accessToken" => $access_token,
				"clientToken" => $client_token,
				"user" => [
					"uuid" => $uid,
					"m_uuid" => $handler->fetchMUuidFromUid($uid),
					"name" => $handler->fetchNameFromUid($uid)
				]
			]);
			} catch (Exception $e) {
				print_r($e->getMessage());
			}
		}
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
