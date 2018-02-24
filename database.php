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
			$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE name = :name');
			$query->bindParam(':name', $value);
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

		$query = $this->pdo->prepare('INSERT INTO auth_emailtokens(uid, email, email_token) VALUES (:uid, :email, :email_token)');
		$query->bindParam(':uid', $uid);
		$query->bindParam(':email', $email);
		$query->bindParam(':email_token', $email_token);
		$query->execute();
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
	 * specific token
	 */
	public function fetchUidFromToken($email_token) {
		$query = $this->pdo->prepare('SELECT uid FROM auth_emailtokens WHERE email_token = :email_token');
		$query->bindParam(':email_token', $email_token);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result) {
			return $result['uid'];
		}
		return false;
	}

	/* Returns the m_uuid affiliated with the token,
	 * otherwise, just returns false for no token.
	 */
	public function fetchMUuid($token) {
		$query = $this->pdo->prepare('SELECT m_uuid FROM auth_tokens WHERE token = :token');
		$query->bindParam(':token', $token);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if ($result) {
			return $result['m_uuid'];
		}
		return false;
	}

	/* Calls to remove the m_uuid affiliated token
	 * from auth_tokens
	 */
	public function removeToken($token) {
		$query = $this->pdo->prepare('REMOVE FROM auth_tokens WHERE token = :token');
		$query->bindParam(':token', $token);
		$query->execute();
	}

	public function setup() {
		try {
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_users(uid VARCHAR(16), m_uuid VARCHAR(32), username VARCHAR(32), password VARCHAR(60), email VARCHAR(128), verified BOOLEAN, UNIQUE KEY(uid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_emailtokens(uid VARCHAR(16), email VARCHAR(64), email_token VARCHAR(16), UNIQUE KEY(uid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_tokens(token VARCHAR(16), m_uuid VARCHAR(32), time INT, UNIQUE KEY(m_uuid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_permissions(uid VARCHAR(16), is_student BOOLEAN, is_builder BOOLEAN, is_mod BOOLEAN, is_admin BOOLEAN, is_host BOOLEAN, UNIQUE KEY(uid))");
			return "Successfully setup the database.";
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
}

try {
	$pdo = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo $e->getMessage();
	die();
}
?>
