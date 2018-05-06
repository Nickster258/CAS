<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

require_once ROOT_DIR . 'vendor/autoload.php';

class Email {

	public $host;
	public $port;
	public $username;
	private $password;
	public $sender;
	public $sender_name;
	private $transport;
	private $mailer;
	private $message;

	public function __construct($host, $port, $username, $password, $sender, $sender_name) {
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->sender = $sender;
		$this->sender_name = $sender_name;
		$this->attemptConnect();
	}

	private function attemptConnect() {
		$this->transport = (new Swift_SmtpTransport($this->host, $this->port, 'ssl'))
			->setUsername($this->username)
			->setPassword($this->password);
		$this->mailer = new Swift_Mailer($this->transport);
	}

	public function sendBasic($subject, $target, $message) {
		$this->message = (new Swift_Message($subject))
			->setFrom(array($this->sender => $this->sender_name))
			->setTo(array($target))
			->setBody($message);
		return $this->mailer->send($this->message);
		echo "done";
	}

	public function send($data, $type) {
		switch($type) {
			case "emailVerification":
				$this->emailVerification($data);
				break;
			case "passwordReset":
				$this->passwordReset($data);
				break;
			case "passwordChange":
				$this->passwordChange($data);
				break;
			case "emailChange":
				$this->emailChange($data);
				break;
		}
	}

	private function emailVerification($data) {
		$body = "Hello " . $data['name'] . ",\n\nThank you for registering!\n\nClick below to verify your email.\n\n" . URL . "user.php?method=verify&token=" . $data['token'] . "\n\nSincerely,\n" . SERVICE_NAME;
		$target = $data['target'];
		$subject = SERVICE_NAME . " - Email Verification";
		$this->sendBasic($subject, $target, $body);
	}

	private function passwordReset($data) {
		$body = "Hello " . $data['name'] . ",\n\nYou have asked to reset your password. Click below to reset your password.\n\n" . URL . "settings.php?method=resetPass&token=" . $data['token'] . "\n\nSincerely,\n" . SERVICE_NAME;
		$target = $data['target'];
		$subject = SERVICE_NAME . " - Password Reset";
		$this->sendBasic($subject, $target, $body);
	}

	private function passwordChange($data) {
		$body = "Hello " . $data['name'] . ",\n\nYour password has been reset. If this was not you, please click below to set a different password.\n\n" . URL . "settings.php?method=resetPass&token=" . $data['token'] . "\n\nIf this was you, you can ignore this message.\n\nSincerely,\n" . SERVICE_NAME;
		$target = $data['target'];
		$subject = SERVICE_NAME . " - Password Reset Confirmation";
		$this->sendBasic($subject, $target, $body);
	}
}

try {
	$email = new Email(EMAIL_HOST, EMAIL_PORT, EMAIL_USERNAME, EMAIL_PASSWORD, EMAIL_SENDER, EMAIL_SENDER_NAME);
} catch (Exception $e) {
	$e->getMessage();
}

?>
