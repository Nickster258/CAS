<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

require_once 'constants.php';

class ApiResponse {

	public $data;

	public function __construct($input) {
		if (isset($input)) {
			$this->data = $input;
		} else {
			$this->data = [
				'Status' => 'OK',
				'Application-Author' => 'Nickster258',
				'Application-Description' => 'Central Authentication Service',
				'Application-Owner' => 'Nickster258',
			];
		}
		$this->respond(200);
	}

	public function respond($response_code) {
		http_response_code($response_code);
		header ('Content-Type: application/json');
		print_r(json_encode($this->data));
	}

}

class ErrorResponse extends ApiResponse {
	
	public function __construct($response_code, $error, $error_message, $cause) {
		$this->data = [
			'error' => $error,
			'errorMessage' => $error_message,
			'cause' => $cause
		];
		$this->respond($response_code);
	}
}

class GeneralResponse extends ApiResponse {

	public function __construct($input) {
		parent::__construct($input);
	}
}

class RefreshResponse extends ApiResponse {

	public function __construct($data) {
	
	}
}

class ValidateResponse extends ApiResponse {

	public function __construct($data) {

	}
}
class SignoutResponse extends ApiResponse {

	public function __construct($data) {
		
	}
}

class InvalidateResponse extends ApiResponse {

	public function __construct($data) {

	}
}
?>
