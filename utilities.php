<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

require_once 'database.php';

function verify_login($handler) {
	if (isset($_COOKIE['cas_auth'])) {
		if (!isset($_SESSION["uid"])) {
			$_SESSION["uid"] = $handler->fetchUidFromToken($_COOKIE['cas_auth']);
		}
		if (!isset($_SESSION["m_uuid"])) {
			$_SESSION["m_uuid"] = $handler->fetchMUuidFromUid($_SESSION["uid"]);
		}
	}
}

function do_response($location) {
	if (isset($_SESSION["response"]) && (strcmp($_SESSION["response"]["location"], $location) === 0)) {
		$status = $_SESSION["response"]["status"];
		$message = $_SESSION["response"]["message"];

		echo "<div class=\"" . $status . "\">" . $message . "</div>";

		unset($_SESSION["response"]);
	}
}

function register_new_user($m_uuid, $name, $hash, $email, $handler) {
	$email_token = get_unique_token($handler);
	$uid = get_unique_id($handler);
	if (is_not_registered($m_uuid, $name, $email, $handler)) {
		$handler->setUnverifiedUser($uid, $m_uuid, $name, $hash, $email, $email_token);
		send_verification_email($email, $email_token);
	} else {
		$response = new RegistrationResponse("alreadyRegistered");
		$response->redirect();
		echo "That Mojang UUID, Name, or email has already been registered";
		session_destroy();
	}
}

function get_unique_token($handler) {
	for ($i = 0; $i<10; $i++) {
		$temp = Random::newRandom(EMAIL_TOKEN_LENGTH, "uid");
		if(!$handler->userValueExists($temp, "email_token")) {
			return $temp;
		}
	}
	echo "Error with token generation.";
}

function get_unique_id($handler) {
	for ($i = 0; $i<10; $i++) {
		$temp = Random::newCryptographicRandom(16);
		if(!$handler->userValueExists($temp, "uid")) {
			return $temp;
		}
	}
	echo "Error with unique ID generation.";
}

function is_not_registered($m_uuid, $name, $email, $handler) {
	if($handler->userValueExists($email, "email") || $handler->userValueExists($name, "name")) {
		return false;
	}
	return true;
}

function send_verification_email($email, $email_token) {
	$response = new RegistrationResponse("sendingEmailVerfication");
	$response->redirect();
	echo "Sending email verification";
}

function is_valid_name($name) {
	if(preg_match('/^[a-z\d_]{3,32}$/i', $name)) {
		return $name;
	}
	return false;
}

function is_valid_email($email) {
	if(preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email)) {
		return $email;
	}
	return false;
}

?>
