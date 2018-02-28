<?php

define('IN_CAS', true);

require_once 'constants.php';
require_once 'random.php';
require_once 'database.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

session_start();

function is_valid_email($email) {
	if(preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email)) {
		return $email;
	}
	return false;
}

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_COOKIE['cas_auth'])) {
		$uid = $handler->fetchUidFromToken($_COOKIE['cas_auth']);
		if($uid) {
			echo "You are already logged in";
			$_SESSION["uid"] = $uid;
		} else {
			echo "Invalid cookie";
			die();
		}
	} else if (isset($_POST["email"]) && isset($_POST["pass"])) {
		$valid_email = is_valid_email($_POST["email"]);
		if ($valid_email && (strlen($_POST["pass"]) > 7)) {
			$uid = $handler->fetchUidFromEmail($valid_email);
			$hash = $handler->fetchHashFromUid($uid);
			if (password_verify(utf8_decode($_POST["pass"]), $hash)) {
				$_SESSION["uid"] = $uid;
				if(isset($_POST['rememberme'])) {
					$remember_me_time = time() + 5184000; // 5184000 = 60 days
					$remember_me_token = Random::newCryptographicRandom(32);
					$handler->setAuthToken($uid, $remember_me_token, $remember_me_time);
					setcookie("cas_auth", $remember_me_token, $remember_me_time, "/", $MYDOMAIN);
					echo "You are now logged in";
				}
			} else {
				echo "Username or password is incorrect";
				die();
			}
		} else {
			echo "Oh no, something is formatted wrong";
			die();
		}
	} else {
		header ("Location: " . $URL . "index.php");
	}
}	
?>
