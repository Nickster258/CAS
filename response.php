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

	}

	public function redirect() {
		header ("Location: " . $this->target);
	}

}

class UserResponse extends Response {

	public function __construct($type) {
		$this->target = $URL . "index.php";
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
			case "loginSuccess":
				$this->loginSuccess();
				break;
		}
	}

	public function invalidFormatting() {
		$this->message = "Something is formatted incorretly";
		$this->status = "failure";
		$this->location = "login_form";
		Response::assembleArray();
	}

	public function invalidInformation() {
		$this->message = "Username or password is incorrect";
		$this->status = "failure";
		$this->location = "login_form";
		Response::assembleArray();
	}

	public function alreadyLogged() {
		$this->message = "You are already logged in";
		$this->status = "neutral";
		$this->location = "login_form";
		Response::assembleArray();
	}

	public function invalidCookie() {
		$this->message = "Invalid cookie";
		$this->status = "failure";
		$this->location = "generic_failure";
		Response::assembleArray();
	}

	public function loginSuccess() {
		$this->message = "You are now logged in";
		$this->status = "success";
		$this->location = "generic_success";
		Response::assembleArray();
	}

}

class RegistrationResponse extends Response {

	public function __construct($type) {
		$this->target = $URL . "index.php";
		$this->type = $type;
		switch($type) {
			case "invalidToken":
				$this->invalidToken();
				break;
			case "noMUuid":
				$this->noMUuid();
				break;
			case "invalidFormatting":
				$this->invalidFormating();
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
			case "registrationSuccess":
				$this->registrationSuccess();
				break;
		}
	}
	

	public function invalidToken() {
		$this->message = "Invalid token";
		$this->status = "failure";
		$this->location = "token_verification";
		Response::assembleArray();
	}

	public function noMUuid() {
		$this->message = "No affiliated Mojang UUID with that token";
		$this->status = "failure";
		$this->location = "token_verification";
		Response::assembleArray();
	}

	public function invalidFormatting() {
		$this->message = "Something is formatted incorrectly";
		$this->status = "failure";
		$this->location = "registration_form";
		Response::assembleArray();
	}
	
	public function passwordMismatch() {
		$this->message = "Your passwords do not match";
		$this->status = "failure";
		$this->location = "registration_form";
		Response::assembleArray();
	}

	public function sendingEmailVerification() {
		$this->message = "Sending email verification";
		$this->status = "success";
		$this->location = "registration_form";
		Response::assembleArray();
	}

	public function alreadyRegistered() {
		$this->message = "That Mojang UUID, Name, or email has already been registered";
		$this->status = "failure";
		$this->location = "registration_form";
		Response::assembleArray();
	}

	public function registrationSuccess() {
		$this->message = "You have successfully registered";
		$this->status = "success";
		$this->location = "registration_form";
		Response::assembleArray();
	}
}

class InternalResponse extends Response {

	public function __construct($type, $data) {
		$this->type = $type;
		switch($type) {
			case "tokenGenerationError":
				tokenGenerationError($data);
				break;
			case "uidGenerationError":
				uidGenerationError($data);
				break;
		}
	}

	public function tokenGenerationError($data) {

	}

	public function uidGenerationError($data) {

	}
}
?>
