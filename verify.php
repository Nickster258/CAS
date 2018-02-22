<?php
require_once 'database.php';

session_start();

if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_GET["token"])) {
		$email_token = $_GET["token"];
		if (preg_math('/[a-z]/i{' . $email_token_length .'}', $email_token)) {
			$uid = $handler->fetchUidFromToken($email_token);
			if($uid != false) {
				if ($handler->verifyUser($uid)) {
					echo "Your account has been verified!";
				} else {
					echo "Verification failure. Blame capo.";
					die();
				}
			} else {
				echo "Invalid token";
				die();
			}

		} else {
			echo "Invalid token";
			die();
		}
	} else {
		die();
	}
}	
?>
