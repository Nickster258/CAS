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
	if (isset($_SESSION["m_uuid"]) && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["pass"]) && isset($_POST["verified_pass"])) {
		$valid_name = is_valid_name($_POST["name"]);
		$valid_email = is_valid_email($_POST["email"]);
		if ($valid_name && $valid_email && (strlen($_POST["pass"]) > 7)) {
			$hash = password_hash(utf8_decode($_POST["pass"]), PASSWORD_BCRYPT);
			if (password_verify(utf8_decode($_POST["verified_pass"]), $hash)) {
				$_SESSION["name"] = $valid_name;
				$_SESSION["email"] = $valid_email;
				unset($_SESSION["token"]);
				$email_token = get_unique_token($handler);
				$uid = get_unique_id($handler);
				$m_uuid = $_SESSION["m_uuid"];
				if (is_not_registered($valid_name, $valid_email, $handler)) {
					$handler->setUnverifiedUser($uid, $m_uuid, $valid_name, $hash, $valid_email, $email_token);
					//send_verification_email($email, $email_token);
					$response = new RegistrationResponse("registrationSuccess");
					$response->redirect();
				} else {
					$response = new RegistrationResponse("alreadyRegistered");
					$response->redirect();
				}
			} else {
				$response = new RegistrationResponse("passwordMismatch");
				$response->redirect();
			}
		} else {
			$response = new RegistrationResponse("invalidFormatting");
			$response->redirect();
		}
	} else if (isset($_GET["token"])) {
		$token = $_GET["token"];
		$_SESSION["token"] = $token;
		if (preg_match('/[a-z0-9]{16}/i', $token)) {
			$m_uuid = $handler->fetchMUuid($token);
			if($m_uuid != false) {
				if(!$handler->userValueExists($m_uuid, "m_uuid")) {
					$_SESSION["m_uuid"] = $m_uuid;
					$location = "Location: " . URL . "index.php";
					header ($location);
				} else {
					$response = new RegistrationResponse("muuidRegistered");
					$response->redirect();
				}	
			} else {
				$response = new RegistrationResponse("noMUuid");
				$response->redirect();
			}
		} else {
			$response = new RegistrationResponse("invalidToken");
			$response->redirect();
		}
	} else {
		header ("Location: " . $URL . "index.php");
	}
}	
?>
