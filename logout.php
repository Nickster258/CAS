<?php

define('IN_CAS', true);

require_once 'includes/constants.php';
require_once 'includes/database.php';
require_once 'includes/response.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

session_start();

if (isset($_COOKIE['cas_auth'])) {
	$handler->removeAuthToken($_COOKIE['cas_auth']);
	unset($_COOKIE['cas_auth']);
	setcookie('cas_auth', null, -1, "/", $MYDOMAIN);
}

unset($_SESSION["uid"]);

$response = new UserResponse("successfulLogout");
$response->redirect();

?>
