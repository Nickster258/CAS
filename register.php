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
	$query = "CREATE TABLE IF NOT EXISTS users(uid VARCHAR(16), mUuid VARCHAR(32), username VARCHAR(32), password VARCHAR(60), salt VARCHAR(\"$saltLength\"), email VARCHAR(60))";
	$result = mysql_query($query, $conn);
}

function registerUser($mUuid, $name, $hash, $salt, $email) {
	global $conn;
	$emailToken = random(16, "rand");
	$uid = random(16, "uid");
	$query = "INSERT INTO users_unverified(uid, mUuid, username, password, salt, email, emailToken) VALUES(\"$uid\", \"$mUuid\", \"$username\", \"$hash\", \"$salt\", \"$email\", \"$emailToken\")";
	$result = mysql_query($query, $conn);
	$queryToken = "INSERT INTO email_tokens(uid, email, emailToken) VALUES (\"$uid\", \"$email\", \"$emailToken\")";
	if ($result) {
		sendVerificationEmail($email, $emailToken);
	} else {
		return false;
	}
}

function sendVerificationEmail($email, $emailToken) {
}

function getUserDetails($uid) {
	global $conn;
	$query = "SELECT * FROM users WHERE uid = binary \"$uid\"";
	$result = mysql_query($query, $conn);
}

function getSalt() {
	return random($saltLength, "rand");
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
		$validName = isValidName($_POST["name"]);
		$validEmail = isValidEmail($_POST["email"]);
		$validPass = isValidPass($_POST["pass"]);
		$validPassVerified = isValidPass($_POST["verifiedpass"]);
		if ($validName && $validEmail && $validPass && $validPassVerified) {
			$salt = getSalt();
			$hashedPass = getHash($validPass, $salt);
			$hashedPassVerified = getHash($validPassVerified, $salt);
			if ($hashedPass != $hashedPassVerified) {
				$_SESSION["name"] = $validName;
				$_SESSION["email"] = $validEmail;
				$_SESSION["pass"] = $validPass;
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
