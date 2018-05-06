<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

require_once 'constants.php';

class Response {

	public $message;
	public $target;
	public $type;
	public $status;
	public $location;
	public $formatted_response;

	public function __construct($message, $target, $type) {
		$this->message = $message;
		$this->target = $target;
		$this->type = $type;
	}

	public function assembleArray() {

		$this->formatted_response = [ 
			'message'	=> $this->message,
			'status'	=> $this->status,
			'location'	=> $this->location
		];

		$_SESSION["response"] = $this->formatted_response;
		$this->redirect();
	}

	public function redirect() {
		header ("Location: " . $this->target);
	}

}

class UserResponse extends Response {

	public function __construct($type) {
		$this->target = URL . "index.php";
		$this->type = $type;
		switch($type) {
			case "invalidFormatting":
				$this->invalidFormatting();
				break;
			case "invalidInformation":
				$this->invalidInformation();
				break;
			case "alreadyLogged":
				$this->alreadyLogged();
				break;
			case "invalidCookie":
				$this->invalidCookie();
				break;
			case "successfulLogin":
				$this->successfulLogin();
				break;
			case "successfulLogout":
				$this->successfulLogout();
				break;
			case "settingsUpdated":
				$this->settingsUpdated();
				break;
			case "passwordMismatch":
				$this->passwordMismatch();
				break;
			case "incorrectPassword":
				$this->incorrectPassword();
				break;
			case "notAuthorized":
				$this->notAuthorized();
				break;
			case "invalidResetToken":
				$this->invalidResetToken();
				break;
			case "resetPasswordMismatch":
				$this->resetPasswordMismatch();
				break;
			case "passwordResetRequest":
				$this->passwordResetRequest();
				break;
			case "noEmailFound":
				$this->noEmailFound();
				break;
			case "successfulLogout":
				$this->successfulLogout();
				break;
			case "successfulVerification":
				$this->successfulVerification();
				break;
			case "invalidEmailToken":
				$this->invalidEmailToken();
				break;
		}
		Response::assembleArray();
	}

	public function invalidFormatting() {
		$this->message = "Something is formatted incorretly";
		$this->status = "failure";
		$this->location = "login_form";
	}

	public function invalidInformation() {
		$this->message = "Username or password is incorrect";
		$this->status = "failure";
		$this->location = "login_form";
	}

	public function alreadyLogged() {
		$this->message = "You are already logged in";
		$this->status = "neutral";
		$this->location = "login_form";
	}

	public function invalidCookie() {
		$this->message = "Invalid cookie";
		$this->status = "failure";
		$this->location = "generic";
	}

	public function successfulLogin() {
		$this->message = "You are now logged in";
		$this->status = "success";
		$this->location = "generic";
	}

	public function successfulLogout() {
		$this->message = "You are now logged out";
		$this->status = "success";
		$this->location = "generic";
	}

	public function settingsUpdated() {
		$this->message = "Your settings have been updated";
		$this->status = "success";
		$this->location = "generic";
		$this->target = URL . "settings.php";
	}

	public function passwordMismatch() {
		$this->message = "Your passwords do not match";
		$this->status = "failure";
		$this->location = "settings_form";
		$this->target = URL . "settings.php";
	}

	public function incorrectPassword() {
		$this->message = "You have entered an incorrect password";
		$this->status = "failure";
		$this->location = "settings_form";
		$this->target = URL . "settings.php";
	}

	public function notAuthorized() {
		$this->message = "You are not authorized to view this";
		$this->status = "failure";
		$this->location = "generic";
	}

	public function invalidResetToken() {
		$this->message = "Invalid reset token";
		$this->status = "failure";
		$this->location = "generic";
	}

	public function resetPasswordMismatch() {
		$this->message = "Your passwords do not match";
		$this->status = "failure";
		$this->location = "settings_form";
		$this->target = URL . "settings.php?method=resetPass";
	}

	public function passwordResetRequest() {
		$this->message = "Reset email sent";
		$this->status = "success";
		$this->location = "generic";
	}

	public function noEmailFound() {
		$this->message = "That email is not registered";
		$this->status = "failure";
		$this->location = "settings_form";
		$this->target = "settings.php?method=requestReset";
	}

	public function successfulVerification() {
		$this->message = "Your account has been verified";
		$this->status = "success";
		$this->location = "generic";
	}

	public function invalidEmailToken() {
		$this->message = "Invalid email token";
		$this->status = "failure";
		$this->location = "generic";
	}
}

class RegistrationResponse extends Response {

	public function __construct($type) {
		$this->target = URL . "index.php";
		$this->type = $type;
		switch($type) {
			case "invalidToken":
				$this->invalidToken();
				break;
			case "noMUuid":
				$this->noMUuid();
				break;
			case "invalidFormatting":
				$this->invalidFormatting();
				break;
			case "passwordMismatch":
				$this->passwordMismatch();
				break;
			case "sendingEmailVerification":
				$this->sendingEmailVerification();
				break;
			case "alreadyRegistered":
				$this->alreadyRegistered();
				break;
			case "muuidRegistered":
				$this->muuidRegistered();
				break;
			case "registrationSuccess":
				$this->registrationSuccess();
				break;
		}
		Response::assembleArray();
	}
	

	public function invalidToken() {
		$this->message = "Invalid token";
		$this->status = "failure";
		$this->location = "generic";
	}

	public function noMUuid() {
		$this->message = "No affiliated Mojang UUID with that token";
		$this->status = "failure";
		$this->location = "generic";
	}

	public function invalidFormatting() {
		$this->message = "Something is formatted incorrectly";
		$this->status = "failure";
		$this->location = "registration_form";
	}
	
	public function passwordMismatch() {
		$this->message = "Your passwords do not match";
		$this->status = "failure";
		$this->location = "registration_form";
	}

	public function sendingEmailVerification() {
		$this->message = "Sending email verification";
		$this->status = "success";
		$this->location = "registration_form";
	}

	public function alreadyRegistered() {
		$this->message = "That Mojang UUID, Name, or email has already been registered";
		$this->status = "failure";
		$this->location = "generic";
	}

	public function muuidRegistered() {
		$this->message = "That Mojang UUID has already been registered";
		$this->status = "failure";
		$this->location = "generic";
	}

	public function registrationSuccess() {
		$this->message = "You have successfully registered. Please log in to continue.";
		$this->status = "success";
		$this->location = "generic";
	}
}
?>
