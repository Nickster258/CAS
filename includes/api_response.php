<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

class ApiResponse {

	public $data;

	public function __construct($input, $response_code) {
		if (isset($input)) {
			$this->data = $input;
		}
		$this->respond($response_code);
	}

	public function respond($response_code) {
		http_response_code($response_code);
		header ('Content-Type: application/json');
		if(isset($this->data)) {
			print_r(json_encode($this->data));
		}
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

	public function __construct($input, $response_code) {
		parent::__construct($input, $response_code);
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
