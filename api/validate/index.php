<?php

define('IN_CAS', true);

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
} elseif (valid_json($_POST)) {
	
} else {
	new ErrorResponse(
		400,
		"Invalid Post Formatting",
		"The post data was formatted incorrectly",
		null
	);
}

?>
