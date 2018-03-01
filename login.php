<?php

define('IN_CAS', true);

require_once 'constants.php';
require_once 'random.php';
require_once 'database.php';
require_once 'response.php';
require_once 'utilities.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

session_start();

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_COOKIE['cas_auth'])) {
		$uid = $handler->fetchUidFromToken($_COOKIE['cas_auth']);
		if($uid) {
			$response = new UserResponse("alreadyLogged");
			$response->redirect();
			$_SESSION["uid"] = $uid;
		} else {
			$response = new UserResponse("invalidCookie");
			$response->redirect();
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
					$response = new UserResponse("loginSuccess");
					$response->redirect();
				}
			} else {
				$response = new UserResponse("invalidInformation");
				$response->redirect();
			}
		} else {
			$response = new UserResponse("invalidFormatting");
			$response->redirect();
		}
	} else {
		header ("Location: " . $URL . "index.php");
	}
}	
?>
