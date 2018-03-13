<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

require_once 'constants.php';

class DatabaseHandler {
	private $pdo;

	public function __construct($pdo) {
		$this->pdo = $pdo;
	}

	/* Checks if the m_uuid, name, or email
	 * is already within the database
	 */
	public function userValueExists($value, $type) {
		if (strcmp($type, "m_uuid") === 0) {
			$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE m_uuid = :m_uuid');
			$query->bindParam(':m_uuid', $value);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return true;
			}
		} else if (strcmp($type, "name") === 0) {
			$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE username = :username');
			$query->bindParam(':username', $value);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return true;
			}
		} else if (strcmp($type, "email") === 0) {
			$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE email = :email');
			$query->bindParam(':email', $value);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return true;
			}
		} else if (strcmp($type, "uid") === 0) {
			$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE uid = :uid');
			$query->bindParam(':uid', $value);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return true;
			}
		} else if (strcmp($type, "email_token") === 0) {
			$query = $this->pdo->prepare('SELECT * FROM auth_emailtokens WHERE email_token = :email_token');
			$query->bindParam(':email_token', $value);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return true;
			}
		}
		return false;
	}

	/* Sets an unverified user in auth_users
	 * and in auth_emailtokens
	 */
	public function setUnverifiedUser($uid, $m_uuid, $name, $hash, $email, $email_token) {
		$query = $this->pdo->prepare('INSERT INTO auth_users(uid, m_uuid, username, password, email, verified) VALUES(:uid, :m_uuid, :username, :password, :email, 0)');
		$query->bindParam(':uid', $uid);
		$query->bindParam(':m_uuid', $m_uuid);
		$query->bindParam(':username', $name);
		$query->bindParam(':password', $hash);
		$query->bindParam(':email', $email);
		$query->execute();

		$query = $this->pdo->prepare('INSERT INTO auth_emailtokens(uid, email, email_token, expires) VALUES (:uid, :email, :email_token, :expires)');
		$query->bindParam(':uid', $uid);
		$query->bindParam(':email', $email);
		$query->bindParam(':email_token', $email_token);
		$expires = time()+86400;
		$query->bindParam(':expires', $expires);
		$query->execute();
	}

	/* Sets an authentication token relative
	 * to the ID that requested it
	 */
	public function setAuthToken($uid, $token, $timeout) {
		try {
			$query = $this->pdo->prepare('INSERT INTO auth_tokens(uid, token, expires) VALUES (:uid, :token, :expires)');
			$query->bindParam(':uid', $uid);
			$query->bindParam(':token', $token);
			$query->bindParam(':expires', $timeout);
			$query->execute();
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
	}
	/* Sets the password for a selected
	 * uid
	 */
	public function setNewPass($uid, $hash) {
		try {
			$query = $this->pdo->prepare('UPDATE auth_users SET password = :hash WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->bindParam(':hash', $hash);
			$query->execute();
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
	}

	/* Verifies the user by uid in auth_users
	 * by setting verified to 1
	 */
	public function verifyUser($uid) {
		try {
			$query = $this->pdo->prepare('UPDATE auth_users SET verified = 1 WHERE uid = :uid');
			$query->bindPAram(':uid', $uid);
			$query->execute();
			return true;
		} catch (PDOException $e) {
			return false;
		}
	}

	/* Returns the uid affiliated with the
	 * specific email token
	 */
	public function fetchUidFromEmailToken($email_token) {
		$query = $this->pdo->prepare('SELECT uid FROM auth_emailtokens WHERE email_token = :email_token');
		$query->bindParam(':email_token', $email_token);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result) {
			return $result['uid'];
		}
		return false;
	}

	/* Returns the name affiliated with the
	 * uid
	 */
	public function fetchNameFromUid($uid) {
		$query = $this->pdo->prepare('SELECT username FROM auth_users WHERE uid = :uid');
		$query->bindParam(':uid', $uid);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result) {
			return $result['username'];
		}
		return false;
	}

	/* Returns the email affiliated with the
	 * uid
	 */
	public function fetchEmailFromUid($uid) {
		$query = $this->pdo->prepare('SELECT email FROM auth_users WHERE uid = :uid');
		$query->bindParam(':uid', $uid);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result) {
			return $result['email'];
		}
		return false;
	}

	/* Returns the uid affiliated with the
	 * specified token
	 */
	public function fetchUidFromToken($token) {
		$query = $this->pdo->prepare('SELECT uid FROM auth_tokens WHERE token = :token');
		$query->bindParam(':token', $token);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result) {
			return $result['uid'];
		}
		return false;
	}

	/* Returns the uid affiliated with the
	 * email of the user
	 */
	public function fetchUidFromEmail($email) {
		$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE email = :email');
		$query->bindParam(':email', $email);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if ($result) {
			return $result['uid'];
		}
		return false;
	}

	/* returns the hash affiliated with the uid
	 * of the user
	 */
	public function fetchHashFromUid($uid) {
		$query = $this->pdo->prepare('SELECT password FROM auth_users WHERE uid = :uid');
		$query->bindParam(':uid', $uid);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if ($result) {
			return $result['password'];
		}
		return false;
	}

	/* Returns the mojang uuid affiliated with
	 * the uid of the user
	 */
	public function fetchMUuidFromUid($uid) {
		$query = $this->pdo->prepare('SELECT m_uuid FROM auth_users WHERE uid = :uid');
		$query->bindParam(':uid', $uid);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result) {
			return $result['m_uuid'];
		}
		return false;
	}

	/* Returns the m_uuid affiliated with the token,
	 * otherwise, just returns false for no token.
	 */
	public function fetchMUuid($token) {
		$query = $this->pdo->prepare('SELECT m_uuid FROM auth_registrationtokens WHERE token = :token');
		$query->bindParam(':token', $token);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if ($result) {
			return $result['m_uuid'];
		}
		return false;
	}

	/* Calls to remove the token from the database 
	 * affiliated with the token data
	 */
	public function removeAuthToken($token) {
		$query = $this->pdo->prepare('DELETE FROM auth_tokens WHERE token = :token');
		$query->bindParam(':token', $token);
		$query->execute();
	}

	/* Calls to remove the m_uuid affiliated token
	 * from auth_tokens
	 */
	public function removeRegistrationToken($token) {
		$query = $this->pdo->prepare('DELETE FROM auth_registrationtokens WHERE token = :token');
		$query->bindParam(':token', $token);
		$query->execute();
	}

	public function setup() {
		try {
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_tokens(uid VARCHAR(32), token VARCHAR(64), expires INTEGER(12))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_users(uid VARCHAR(32), m_uuid VARCHAR(32), username VARCHAR(32), password VARCHAR(60), email VARCHAR(128), verified BOOLEAN, UNIQUE KEY(uid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_emailtokens(uid VARCHAR(32), email VARCHAR(64), email_token VARCHAR(16), expires INTEGER(12), UNIQUE KEY(uid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_registrationtokens(token VARCHAR(16), m_uuid VARCHAR(32), time INT, UNIQUE KEY(m_uuid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_permissions(uid VARCHAR(32), is_student BOOLEAN, is_builder BOOLEAN, is_mod BOOLEAN, is_admin BOOLEAN, is_host BOOLEAN, UNIQUE KEY(uid))");
			return "Successfully setup the database.";
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function echoStatus() {
		$attributes = array(
			"AUTOCOMMIT", "ERRMODE", "CASE", "CLIENT_VERSION", "CONNECTION_STATUS",
			"ORACLE_NULLS", "PERSISTENT" 
		);

		foreach ($attributes as $val) {
			echo "PDO::ATTR_$val: ";
			echo $this->pdo->getAttribute(constant("PDO::ATTR_$val")) . "<br>\r\n";
		}
	}
}

try {
	$pdo = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo $e->getMessage();
	die();
}
?>
