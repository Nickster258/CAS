<?php
require __DIR__ . '/constants.php';
require __DIR__ . '/random.php';

$conn = mysql_connect($dbhost, $dbuser, $dbpass);

mysql_select_db($db, $conn);

if(!$conn) {
	die(mysql_error());
	echo "hi";
} else {
	global $conn;
	$query = "CREATE TABLE IF NOT EXISTS users(uid VARCHAR(16), username VARCHAR(32), password VARCHAR(60), salt VARCHAR(8))";
	$result = mysql_query($query, $conn);
}

function addUser($uid, $username, $hash, $salt) {
	global $conn;
	$query = "INSERT INTO users(uid, username, password, salt) VALUES(\"$uid\", \"$username\", \"$hash\", \"$salt\")";
	$result = mysql_query($query, $conn);
	if ($result) {
		return $uid;
	} else {
		return false;
	}
}

function getUserDetails($uid) {
	global $conn;
	$query = "SELECT * FROM users WHERE uid = binary \"$uid\"";
	$result = mysql_query($query, $conn);
	

}

function getUid() {
	global $conn;
	while (true) {
		$randUid = random(16, "uid");
		$query = "SELECT * FROM users WHERE uid = binary \"$randUid\"";
		$result = mysql_query($query, $conn);

		if (!$result) {
			die(mysql_error());
		} elseif (mysql_num_rows($result)==0) {
			return $randUid;
		}
	}
}

function getSalt() {
	return random(8, "rand");
}

function getHash($password, $salt) {
	return password_hash($password . $salt, PASSWORD_BCRYPT);
}


if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_POST["pass"], $_POST["name"])) {
		$pass = $_POST["pass"];
		$name = $_POST["name"];

		$uid = getUid();
		$salt = getSalt();
		$hash = getHash($pass, $salt);

		$result = addUser($uid, $name, $hash, $salt);
		if ($result != false) {
			echo getUserDetails($result);
		} else {
			echo "uhoh";
		}

	} else {
		echo "what";
		die();
	}
} else {
	if (isset($argv[1], $argv[2])) {
		$pass = $argv[1];
		$name = $argv[2];

		$uid = getUid();
		$salt = getSalt();
		$hash = getHash($pass, $salt);

		addUser($uid, $name, $hash, $salt);

	} else {
		echo "what";
		die();
	}
}
?>
