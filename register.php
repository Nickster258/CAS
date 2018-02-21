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
	$query = "CREATE TABLE IF NOT EXISTS users(uid VARCHAR(16), m_uuid VARCHAR(32), username VARCHAR(32), password VARCHAR(60), salt VARCHAR(\"$salt_length\"), email VARCHAR(60), verified BOOLEAN)";
	$result = mysql_query($query, $conn);
}

function register_user($m_uuid, $name, $hash, $salt, $email) {
	global $conn;
	$emailToken = random(16, "rand");
	$uid = random(16, "uid");
	$query = "INSERT INTO users(uid, mUuid, username, password, salt, email, emailToken, verified) VALUES(\"$uid\", \"$mUuid\", \"$username\", \"$hash\", \"$salt\", \"$email\", \"$email_token\", false)";
	$result = mysql_query($query, $conn);
	$query_token = "INSERT INTO email_tokens(uid, email, emailToken) VALUES (\"$uid\", \"$email\", \"$email_token\")";
	if ($result) {
		send_verification_email($email, $email_token);
	} else {
		return false;
	}
}

function send_verification_email($email, $email_token) {
}

function get_user_details($uid) {
	global $conn;
	$query = "SELECT * FROM users WHERE uid = binary \"$uid\"";
	$result = mysql_query($query, $conn);
}

function get_salt() {
	return random($salt_length, "rand");
}

function get_hash($password, $salt) {
	return password_hash($password . $salt, PASSWORD_BCRYPT);
}

function get_muuid($token) {
}

function is_valid_name($name) {
}

function is_valid_email($email) {
}

function is_valid_pass($pass) {
}

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_SESSION["m_uuid"]) && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["pass"]) && isset($_POST["verified_pass"])) {
		$valid_name = is_valid_name($_POST["name"]);
		$valid_email = is_valid_email($_POST["email"]);
		$valid_pass = is_valid_pass($_POST["pass"]);
		$valid_pass_verified = is_valid_pass($_POST["verified_pass"]);
		if (!$valid_name && !$valid_email && !$valid_pass && !$valid_pass_verified) {
			$salt = get_salt();
			$hashed_pass = get_hash($valid_pass, $salt);
			$hashed_pass_verified = get_hash($valid_pass_verified, $salt);
			if ($hashed_pass != $hashed_pass_verified) {
				$_SESSION["name"] = $valid_name;
				$_SESSION["email"] = $valid_email;
				$_SESSION["pass"] = $valid_pass;
				register_user($m_uuid, $name, $hash, $email, $salt);
			} else {
				echo "Your passwords do not match!";
				die();
			}
		} else {
			echo "Oh no, something is formatted! wrong";
			die();
		}
	} else if (isset($_GET["token"])) {
		$token = $_GET["token"];
		$_SESSION["token"] = $token;
		if (preg_math('/[a-z]/i', $token) && is_valid_token($token)) {
			$mUuid = get_muuid($token);
			$_SESSION["m_uuid"] = $mUuid;
			$location = "Location: " . $URL . "register.php";
			header ($location);
		}
	} else {
		die();
	}
}	
?>
