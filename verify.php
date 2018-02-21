<?php
require __DIR__ . '/constants.php';
require __DIR__ . '/random.php';

session_start();

$conn = mysql_connect($dbhost, $dbuser, $dbpass);

mysql_select_db($db, $conn);

if(!$conn) {
	die(mysql_error());
	echo "hi";
} else {
	global $conn;
}

function verify_user($uid) {
	global $conn;
	$query = "UPDATE auth_users SET verified = 1 WHERE uid = \"$uid\"";
	$result = mysql_query($query, $conn);
}

function is_valid_token($email_token) {
}

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_GET["token"])) {
		$email_token = $_GET["token"];
		$_SESSION["email_token"] = $email_token;
		if (preg_math('/[a-z]/i', $email_token) && is_valid_token($email_token)) {
			$uid = verify_token($email_token);
			if($uid != false) {
				if (verify_user($uid)) {
					echo "Your account has been verified!";
				} else {
					echo "Verification failure. Blame capo.";
					die();
				}
			} else {
				echo "Invalid token";
				die();
			}

		} else {
			echo "Invalid token";
			die();
		}
	} else {
		die();
	}
}	
?>
