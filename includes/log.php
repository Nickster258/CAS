<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

require_once 'constants.php';

class ErrorLog {

	public $vars;
	public $message;
	public $time;
	public $type;

	public function __construct($vars, $message, $type) {
		$this->vars = $vars;	
		$this->message = $message;
		$this->type = $type;
		$this->time = time();
		writeToFile();
	}

	private function writeToFile() {
		$contents = file_get_contents(LOG_FILE);
		$contents .= $this->time . "\t" . $this->type . "\t" . $this->vars . "\t" . $this->message;
		file_put_contents(LOG_FILE, $contents);
	}
}

?>
