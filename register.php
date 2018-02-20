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

function registerUser($m_uuid, $name, $hash, $salt, $email) {
	global $conn;
	$emailToken = random(16, "rand");
	$uid = random(16, "uid");
	$query = "INSERT INTO users(uid, mUuid, username, password, salt, email, emailToken, verified) VALUES(\"$uid\", \"$mUuid\", \"$username\", \"$hash\", \"$salt\", \"$email\", \"$emailToken\", false)";
	$result = mysql_query($query, $conn);
	$query_token = "INSERT INTO email_tokens(uid, email, emailToken) VALUES (\"$uid\", \"$email\", \"$email_token\")";
	if ($result) {
		sendVerificationEmail($email, $email_token);
	} else {
		return false;
	}
}

function sendVerificationEmail($email, $email_token) {
}

function getUserDetails($uid) {
	global $conn;
	$query = "SELECT * FROM users WHERE uid = binary \"$uid\"";
	$result = mysql_query($query, $conn);
}

function getSalt() {
	return random($salt_length, "rand");
}

function getHash($password, $salt) {
	return password_hash($password . $salt, PASSWORD_BCRYPT);
}

function isValidName($name) {
}

function isValidEmail($email) {
}

function isValidPass($pass) {
}

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_SESSION["mUuid"]) && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["pass"]) && isset($_POST["verifiedpass"])) {
		$valid_name = isValidName($_POST["name"]);
		$valid_email = isValidEmail($_POST["email"]);
		$valid_pass = isValidPass($_POST["pass"]);
		$valid_pass_verified = isValidPass($_POST["verifiedpass"]);
		if ($valid_name && $valid_email && $valid_pass && $valid_pass_verified) {
			$salt = getSalt();
			$hashed_pass = getHash($valid_pass, $salt);
			$hashed_pass_verified = getHash($valid_pass_verified, $salt);
			if ($hashed_pass != $hashed_pass_verified) {
				$_SESSION["name"] = $valid_name;
				$_SESSION["email"] = $valid_email;
				$_SESSION["pass"] = $valid_pass;
				registerUser($mUuid, $name, $hash, $email, $salt);
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
		if (preg_math('/[a-z]/i', $token) && isValidToken($token)) {
			$mUuid = getmUuid($token);
			$_SESSION["mUuid"] = $mUuid;
			$location = "Location: " . $URL . "register.php";
			header ($location);
		}
	} else {
		die();
	}
}	
?>
