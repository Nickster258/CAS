<?php

define('IN_CAS', true);

require_once '../includes/api_response.php';

new ApiResponse([
	'Status' => 'OK',
	'Application-Author' => 'Nicholas Stonecipher',
	'Application-Description' => 'Central Authentication Service',
	'Application-Owner' => 'Nicholas Stonecipher'
], 200);

?>
