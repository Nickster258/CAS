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
	$query = "CREATE TABLE IF NOT EXISTS users(uid VARCHAR(32), username VARCHAR(32), password VARCHAR(60), salt VARCHAR(8), email VARCHAR(60))";
	$result = mysql_query($query, $conn);
}

function verifyUser($uid) {
	global $conn;
	$query = "SELECT * FROM users_unverified WHERE uid = binary \"$uid\"";
	$result = mysql_query($query, $conn);
	if ($result) {
		
		$query = "INSERT INTO users_unverified(uid, username, password, salt, email, emailToken) VALUES(\"$uid\", \"$username\", \"$hash\", \"$salt\", \"$email\", \"$emailToken\")";
		$result = mysql_query($query, $conn);
		if ($result) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_GET["token"])) {
		$emailToken = $_GET["token"];
		$_SESSION["emailToken"] = $emailToken;
		if (preg_math('/[a-z]/i', $emailToken) && isValidToken($emailToken)) {
			$uid = verifyToken($emailToken);
			if($uid != false) {
				if (verifyUser($uid)) {
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
