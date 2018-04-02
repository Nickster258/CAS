<?php

define('IN_CAS', true);

require_once 'includes/constants.php';
require_once 'includes/random.php';
require_once 'includes/database.php';
require_once 'includes/response.php';
require_once 'includes/utilities.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

session_start();

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_SESSION["uid"]) && isset($_POST["old_pass"]) && isset($_POST["new_pass"]) && isset($_POST["new_pass_verify"])) {
		$uid = $_SESSION["uid"];
		$details = $handler->fetchDetailsFromUid($uid);
		$hash = $details[0]['password'];
		if (password_verify($_POST["old_pass"], $hash)) {
			$new_hash = password_hash($_POST["new_pass"], PASSWORD_BCRYPT);
			echo $new_hash;
			if (password_verify($_POST["new_pass_verify"], $new_hash)) {
				$handler->setNewPass($uid, $new_hash);
				new UserResponse("settingsUpdated");
			} else {
				new UserResponse("passwordMismatch");
			}
		} else {
			new UserResponse("incorrectPassword");
		}
	} else if (isset($_COOKIE['cas_auth'])) {
		$uid = $handler->fetchUidFromToken($_COOKIE['cas_auth']);
		if($uid) {
			new UserResponse("alreadyLogged");
			$_SESSION["uid"] = $uid;
		} else {
			new UserResponse("invalidCookie");
		}
	} else if (isset($_POST["email"]) && isset($_POST["pass"])) {
		$valid_email = is_valid_email($_POST["email"]);
		if ($valid_email && (strlen($_POST["pass"]) > 7)) {
			$uid = $handler->fetchUidFromEmail($valid_email);
			$details = $handler->fetchDetailsFromUid($uid);
			$hash = $details[0]['password'];
			if (password_verify(utf8_decode($_POST["pass"]), $hash)) {
				$_SESSION["uid"] = $uid;
				$_SESSION["m_uuid"] = $details[0]['m_uuid'];
				$_SESSION["name"] = $details[0]['username'];
				$_SESSION["email"] = $details[0]['email'];
				if(isset($_POST['rememberme'])) {
					$remember_me_time = time();
					$expire_time = $remember_me_time + 5184000;
					$remember_me_token = Random::newCryptographicRandom(32);
					$handler->setAuthToken($uid, $remember_me_token, $remember_me_time);
					setcookie("cas_auth", $remember_me_token, $expire_time, "/", DOMAIN);
				}
				new UserResponse("successfulLogin");
			} else {
				new UserResponse("invalidInformation");
			}
		} else {
			new UserResponse("invalidFormatting");
		}
	} else {
		header ("Location: " . $URL . "index.php");
	}
}	
?>
