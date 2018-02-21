<?php
require __DIR__ . '/constants.php';
require __DIR__ . '/random.php';
require __DIR__ . '/connect.php';

session_start();

function register_user($m_uuid, $name, $hash, $salt, $email) {
	$email_token = random(16, "rand");
	$uid = get_unique_id(random(16, "uid"));
	if (is_not_registered($m_uuid, $name, $email)) {
		set_unverified_user($uid, $m_uuid, $name, $hash, $salt, $email, $email_token);
	} else {
		echo "That Mojang UUID, Name, or email has already been registered";
	}
}

function is_not_registered($m_uuid, $name, $email) {
}

function set_unverified_user($uid, $m_uuid, $name, $hash, $salt, $email, $email_token) {
	$query = "REPLACE INTO users(uid, m_uuid, username, password, salt, email, verified) VALUES(?, ?, ?, ?, ?, ?, false)";
        $prepared_query = $handle->prepare($query);
	$prepared_query->execute(array($uid, $m_uuid, $name, $hash, $salt, $email);
        $query_token = "REPLACE INTO email_tokens(uid, email, email_token) VALUES (?, ?, ?)";
	$prepared_query_token = $handle->prepare($query_token);
	$prepared_query->execute(array($uid, $email, $email_token));
}

function send_verification_email($email, $email_token) {
}

function get_user_details($uid) {
	$query = $handle->query("SELECT * FROM users WHERE uid = binary ?");
	return $query;
}

function get_salt() {
	return random($salt_length, "rand");
}

function get_hashed($password, $salt) {
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
			$hashed_pass = get_hashed($valid_pass, $salt);
			$hashed_pass_verified = get_hashed($valid_pass_verified, $salt);
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
