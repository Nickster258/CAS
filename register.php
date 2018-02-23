<?php

define('IN_CAS', true);

require_once 'constants.php';
require_once 'random.php';
require_once 'database.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

session_start();

function register_new_user($m_uuid, $name, $hash, $email) {
	$email_token = get_unique_token();
	$uid = get_unique_id();
	if (is_not_registered($m_uuid, $name, $email)) {
		$handler->setUnverifiedUser($uid, $m_uuid, $name, $hash, $email, $email_token);
		send_verification_email($email, $email_token);
	} else {
		echo "That Mojang UUID, Name, or email has already been registered";
	}
}

function get_unique_token() {
	for ($i = 0; $i<<10; $i++) {
		$temp_token = Random::newRandom(16, "token");
		if(!$handler->userValueExists($temp_token, "email_token")) {
			return $temp_token;
		}
	}
	echo "Error with token generation.";
	die();
}

function get_unique_id() {
	for ($i = 0; $i<<10; $i++) {
		$temp_uid = Random::newRandom(16, "uid");
		if(!$handler->userValueExists($temp_uid, "uid")) {
			return $temp_uid;
		}
	}
	echo "Error with unique ID generation.";
	die();
}

function is_not_registered($m_uuid, $name, $email) {
	if($handler->userValueExists($m_uuid, "m_uuid") || $handler->userValueExists($name, "name") || $handler->userValueExists($email, "email")) {
		return false;
	}
	return true;
}

function send_verification_email($email, $email_token) {

}

function get_hashed($password) {
	return password_hash($password, PASSWORD_BCRYPT);
}

function is_valid_name($name) {
	if(preg_match('~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u', $name)) {
		return $name;
	}
	return false;
}

function is_valid_email($email) {
	if(preg_match("/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD", $email)) {
		return $email;
	}
	return false;
}

function is_valid_pass($pass) {
	if(preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,32}$/", $pass)) {
		return $pass;
	}
	return false;
}

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_SESSION["m_uuid"]) && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["pass"]) && isset($_POST["verified_pass"])) {
		$valid_name = is_valid_name($_POST["name"]);
		$valid_email = is_valid_email($_POST["email"]);
		$valid_pass = is_valid_pass($_POST["pass"]);
		$valid_pass_verified = is_valid_pass($_POST["verified_pass"]);
		if (!$valid_name && !$valid_email && !$valid_pass && !$valid_pass_verified) {
			$hashed_pass = get_hashed($valid_pass);
			$hashed_pass_verified = get_hashed($valid_pass_verified);
			if (strcmp($hashed_pass, $hashed_pass_verified) === 0) {
				$_SESSION["name"] = $valid_name;
				$_SESSION["email"] = $valid_email;
				$_SESSION["pass"] = $valid_pass;
				register_new_user($m_uuid, $name, $hash, $email);
			} else {
				echo "Your passwords do not match!";
				die();
			}
		} else {
			echo "Oh no, something is formatted wrong!";
			die();
		}
	} else if (isset($_GET["token"])) {
		$token = $_GET["token"];
		$_SESSION["token"] = $token;
		if (preg_match('/[a-z0-9]{16}/i', $token)) {
			$m_uuid = $handler->fetchMUuid($token);
			if($m_uuid != false) {
				$_SESSION["m_uuid"] = $m_uuid;
				$location = "Location: " . $URL . "index.php";
				header ($location);
			} else {
				echo "No affiliated Mojand UUID with that token.";
				session_destroy();
				die();
			}
		} else {
			echo "Invalid token.";
			session_destroy();
			die();
		}
	} else {
		header ("Location: " . $URL . "index.php");
	}
}	
?>
