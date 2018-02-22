<?php
require_once 'database.php';

session_start();
/*
function verify_user($uid) {
	try {
		$query = $handle->prepare("UPDATE auth_users SET verified = 1 WHERE uid = ?");
		$query->execute($uid);
		$query->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		return false;
	}
	return true;
}

function verify_token($email_token) {
	$query = $handle->prepare("SELECT uid FROM auth_emailtokens WHERE email_token = binary ?");
	$query->execute($email_token));
	$query->fetch(PDO::FETCH_ASSOC);
	if ($query->rowCount() > 0) {
		return $query["uid"];
	}
	return false;
}
 */
if (isset($_SERVER["REQUEST_METHOD"])) {
	if (isset($_GET["token"])) {
		$email_token = $_GET["token"];
		if (preg_math('/[a-z]/i{' . $email_token_length .'}', $email_token)) {
			$uid = verify_token($email_token);
			if($uid != false) {
				if (verify_user($uid)) {
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
