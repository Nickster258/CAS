<?php

define('IN_CAS', true);

require_once 'includes/constants.php';
require_once ROOT_DIR . 'includes/email.php';
require_once ROOT_DIR . 'includes/random.php';
require_once ROOT_DIR . 'includes/database.php';
require_once ROOT_DIR . 'includes/response.php';
require_once ROOT_DIR . 'includes/utilities.php';

global $pdo;
global $email;

$handler = new DatabaseHandler($pdo);

session_start();

if(!isset($_GET["method"])) {
	die("No method specified");
}
switch($_GET["method"]) {
	case "resetPass":
		if (is_post()) {
			resetPass($handler, $email);
		}
		break;
	case "setPass":
		if (is_post()) {
			setPass($handler);
		}
		break;
	case "login":
		if (is_post()) {
			login($handler);
		}
		break;
	case "sendReset":
		if (is_post()) {
			sendReset($handler, $email);
		}
		break;
	case "verify":
		verify($handler);
		break;
	case "logout":
		logout($handler);
		break;
	default:
		die("Unsupported request type");
}

function resetPass($handler, $email) {
	if (isset($_SESSION["uid"]) && isset($_POST["old_pass"]) && isset($_POST["new_pass"]) && isset($_POST["new_pass_verify"])) {
		$uid = $_SESSION["uid"];
		$details = $handler->fetchDetailsFromUid($uid);
		$hash = $details['password'];
		if (password_verify($_POST["old_pass"], $hash)) {
			$new_hash = password_hash($_POST["new_pass"], PASSWORD_BCRYPT);
			if (password_verify($_POST["new_pass_verify"], $new_hash)) {
				$token = get_unique_reset_token($handler);
				$time = time();
				$handler->setResetToken($uid, $token, $time);
				$handler->setNewPass($uid, $new_hash);
				$data = [
					'user' => $_SESSION["username"],
					'target' => $_SESSION["email"],
					'token' => $token];
				$email->send($data, "passwordChange");
				new UserResponse("settingsUpdated");
			} else {
				new UserResponse("passwordMismatch");
			}
		} else {
			new UserResponse("incorrectPassword");
		}
	} 
}

function sendReset($handler, $email) {
	if($uid = $handler->fetchUidFromEmail($_POST['email'])) {
		$token = get_unique_reset_token($handler);
		$time = time();
		$handler->setResetToken($uid, $token, $time);
		$username = $handler->fetchNameFromUid($uid);
		$data = [
			'user' => $username,
			'target' => $_POST["email"],
			'token' => $token];
		$email->send($data, "passwordReset");
		new UserResponse("passwordResetRequest");
	} else {
		new UserResponse("noEmailFound");
	}
}

function setPass($handler) {
	if (isset($_SESSION['reset_token']) && isset($_POST["new_pass"]) && isset($_POST["new_pass_verify"])) {
		if($uid = $handler->fetchUidFromResetToken($_SESSION['reset_token'])) {
			$hash = password_hash($_POST["new_pass"], PASSWORD_BCRYPT);
			if (password_verify($_POST["new_pass_verify"], $hash)) {
				$handler->setNewPass($uid, $hash);
				$handler->removeResetTokens($uid);
				unset($_SESSION['reset_token']);
				new UserResponse("settingsUpdated");
			} else {
				new UserResponse("resetPasswordMismatch");
			}
		} else {
			new UserResponse("invalidResetToken");
		}
	} else {
		echo "missing required data";
	}
}

function login($handler) { 
	if (isset($_COOKIE['cas_auth'])) {
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
			$hash = $details['password'];
			if (password_verify($_POST["pass"], $hash)) {
				$_SESSION["uid"] = $uid;
				$_SESSION["m_uuid"] = $details['m_uuid'];
				$_SESSION["name"] = $details['username'];
				$_SESSION["email"] = $details['email'];
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

function logout($handler) {
	if (isset($_COOKIE['cas_auth'])) {
		$handler->removeAuthToken($_COOKIE['cas_auth']);
		unset($_COOKIE['cas_auth']);
		setcookie('cas_auth', null, -1, "/", DOMAIN);
	}
	unset($_SESSION["uid"]);
	new UserResponse("successfulLogout");
}

function verify($handler) {
	if ($uid = $handler->fetchUidFromEmailToken($_GET['token'])) {
		$handler->verifyUser($uid);
		new UserResponse("successfulVerification");
	} else {
		new UserResponse("invalidEmailToken");
	}
}
?>
