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

function register_user($m_uuid, $name, $hash, $salt, $email) {
	global $conn;
	$email_token = random(16, "rand");
	$uid = random(16, "uid");
	$escaped_name = mysql_real_escape_string($name);
	$escaped_email = mysql_real_escape_string($email);
	$query = "INSERT INTO users(uid, m_uuid, username, password, salt, email, emailToken, verified) VALUES(\"$uid\", \"$m_uuid\", \"$escaped_name\", \"$hash\", \"$salt\", \"$escaped_email\", \"$email_token\", false)";
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
	if(preg_match('~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u', $name)) {
		return $name;
	} else {
		return false;
	}
}

function is_valid_email($email) {
	if(preg_match("/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD", $email)) {
		return $email;
	} else {
		return false;
	}
}

function is_valid_pass($pass) {
	if(preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}$/", $pass)) {
		return $pass;
	} else {
		return false;
	}
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
		if (preg_match('/[a-z]/i', $token) && is_valid_token($token)) {
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
