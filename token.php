<?php
require __DIR__ . '/constants.php';
require __DIR__ . '/random.php';

session_start();

$conn = mysql_connect($dbhost, $dbuser, $dbpass);

mysql_select_db($db, $conn);

if(!$conn) {
	die(mysql_error());
} else {
	global $conn;
}

function getUuid($token) {
	// TODO
}

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_GET["token"])) {
		$token = $_GET["token"];
		$_SESSION["token"] = $token;
		if (preg_math('/[a-z]/i', $token) && isValidToken($token)) {
			$uuid = getUuid($token);
			$_SESSION["uuid"] = $uuid;
			$location = "Location: " . $URL . "register.php"; 
			header ($location);
		}
	} else {
		echo "what";
		die();
	}
}	
?>
