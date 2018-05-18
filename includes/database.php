<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

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

	/* Returns all information of a group
	 * from its group ID
	 */
	public function fetchGroupDetails($group_level) {
		try {
			$query = $this->pdo->prepare('SELECT * FROM auth_groups WHERE group_level = :group_level');
			$query->bindParam(':group_level', $group_level);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result;
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Removes all tokens affiliated with
	 * the uid
	 */
	public function removeApiTokens($uid) {
		try {
			$query = $this->pdo->prepare('DELETE FROM auth_apitokens WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Checks if the reset token exists
	 */
	public function resetTokenExists($token) {
		try {
			$query = $this->pdo->prepare('SELECT * FROM auth_resettokens WHERE token = :token');
			$query->bindParam(':token', $token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Checks if the access_token for the
	 * api exists
	 */
	public function apiAccessTokenExists($token) {
		try {
			$query = $this->pdo->prepare('SELECT * FROM auth_apitokens WHERE access_token = :access_token');
			$query->bindParam(':access_token', $token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Checks if the client_token for the
	 * specified user exists
	 */
	public function apiClientTokenExists($uid, $client_token) {
		try {
			if ($client_token == null) {
				return false;
			}
			$query = $this->pdo->prepare('SELECT * FROM auth_apitokens WHERE (uid = :uid AND client_token = :client_token)');
			$query->bindParam(':uid', $uid);
			$query->bindParam(':client_token', $client_token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Removes the access and client token
	 * pair
	 */
	public function apiRemoveTokenPair($access_token, $client_token) {
		try {
			if (isset($client_token)) {	
				$query = $this->pdo->prepare('DELETE FROM auth_apitokens WHERE (access_token = :access_token AND client_token = :client_token)');
				$query->bindParam(':access_token', $access_token);
				$query->bindParam(':client_token', $client_token);
				$query->execute();
				$query->debugDumpParams();
			} else {
				$query = $this->pdo->prepare('DELETE FROM auth_apitokens WHERE access_token = :access_token');
				$query->bindParam(':access_token', $access_token);
				$query->execute();
			}
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;	
		}
	}

	/* Updates the acccess token
	 */
	public function apiUpdateAccessToken($access_token, $client_token) {
		try {
			$query = $this->pdo->prepare('UPDATE auth_apitokens SET access_token = :access_token WHERE client_token = :client_token');
			$query->bindParam(':access_token', $access_token);
			$query->bindParam(':client_token', $client_token);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Checks if the pair of client
	 * and access token matches
	 */
	public function apiGetAccessDetails($access_token) {
		try {
			$query = $this->pdo->prepare('SELECT * FROM auth_apitokens WHERE access_token = :access_token');
			$query->bindParam(':access_token', $access_token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result;
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Sets an access_token in api_tokens
	 */
	public function setAccessToken($access_token, $client_token, $uid, $time) {
		try{ 
			$query = $this->pdo->prepare('INSERT INTO auth_apitokens(access_token, client_token, uid, time) VALUES(:access_token, :client_token, :uid, :time)');
			$query->bindParam(':access_token', $access_token);
			$query->bindParam(':client_token', $client_token);
			$query->bindParam(':uid', $uid);
			$query->bindParam(':time', $time);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Sets a reset token for use with the
	 * affiliate uid
	 */
	public function setResetToken($uid, $token, $time) {
		try {
			$query = $this->pdo->prepare('INSERT INTO auth_resettokens(uid, token, time) VALUES(:uid, :token, :time)');
			$query->bindParam(':uid', $uid);
			$query->bindParam(':token', $token);
			$query->bindParam(':time', $time);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Sets an unverified user in auth_users
	 * and in auth_emailtokens
	 */
	public function setUnverifiedUser($uid, $m_uuid, $name, $hash, $email, $email_token) {
		try {
			$query = $this->pdo->prepare('INSERT INTO auth_users(uid, m_uuid, username, password, email, verified, group_level) VALUES(:uid, :m_uuid, :username, :password, :email, 0, 0)');
			$query->bindParam(':uid', $uid);
			$query->bindParam(':m_uuid', $m_uuid);
			$query->bindParam(':username', $name);
			$query->bindParam(':password', $hash);
			$query->bindParam(':email', $email);
			$query->execute();

			$query = $this->pdo->prepare('INSERT INTO auth_emailtokens(uid, email, email_token, time) VALUES (:uid, :email, :email_token, :time)');
			$query->bindParam(':uid', $uid);
			$query->bindParam(':email', $email);
			$query->bindParam(':email_token', $email_token);
			$time = time();
			$query->bindParam(':time', $time);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Sets an authentication token relative
	 * to the ID that requested it
	 */
	public function setAuthToken($uid, $token, $time) {
		try {
			$query = $this->pdo->prepare('INSERT INTO auth_tokens(uid, token, time) VALUES (:uid, :token, :time)');
			$query->bindParam(':uid', $uid);
			$query->bindParam(':token', $token);
			$query->bindParam(':time', $time);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Fetches the uid with with the
	 * affiliated token
	 */
	public function fetchUidFromResetToken($token) {
		try {
			$query = $this->pdo->prepare('SELECT uid FROM auth_resettokens WHERE token = :token');
			$query->bindParam(':token', $token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result['uid'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return true;
		}
	}

	/* Fetches the time the token
	 * was set
	 */
	public function fetchTimeFromToken($token) {
		try {
			$query = $this->pdo->prepare('SELECT time FROM auth_tokens WHERE token = :token');
			$query->bindParam(':token', $token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result['time'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
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
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Verifies the user by uid in auth_users
	 * by setting verified to 1
	 */
	public function verifyUser($uid) {
		try {
			$query = $this->pdo->prepare('UPDATE auth_users SET verified = 1 WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			return true;
		} catch (PDOException $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Removes the email token
	 * affiliated with the uid
	 */
	public function removeEmailToken($uid) {
		try {
			$query = $this->pdo->prepare('DELETE FROM auth_emailtokens WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Returns the uid affiliated with the
	 * specific email token
	 */
	public function fetchUidFromEmailToken($email_token) {
		try {
			$query = $this->pdo->prepare('SELECT uid FROM auth_emailtokens WHERE email_token = :email_token');
			$query->bindParam(':email_token', $email_token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return $result['uid'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Returns the name affiliated with the
	 * uid
	 */
	public function fetchNameFromUid($uid) {
		try {
			$query = $this->pdo->prepare('SELECT username FROM auth_users WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return $result['username'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Returns the email affiliated with the
	 * uid
	 */
	public function fetchEmailFromUid($uid) {
		try {
			$query = $this->pdo->prepare('SELECT email FROM auth_users WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return $result['email'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Returns the uid affiliated with the
	 * specified token
	 */
	public function fetchUidFromToken($token) {
		try {
			$query = $this->pdo->prepare('SELECT uid FROM auth_tokens WHERE token = :token');
			$query->bindParam(':token', $token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return $result['uid'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Returns all the details from a user
	 * based on the uid
	 */
	public function fetchDetailsFromUid($uid) {
		try {
			$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result;
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Returns the uid affiliated with the
	 * email of the user
	 */
	public function fetchUidFromEmail($email) {
		try {
			$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE email = :email');
			$query->bindParam(':email', $email);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result['uid'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}


	/* Returns the uid affiliated with the 
	 * username of the user
	 */
	public function fetchUidFromName($username) {
		try {
			$query = $this->pdo->prepare('SELECT * FROM auth_users WHERE username = :username');
			$query->bindParam(':username', $username);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result['uid'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* returns the hash affiliated with the uid
	 * of the user
	 */
	public function fetchHashFromUid($uid) {
		try {
			$query = $this->pdo->prepare('SELECT password FROM auth_users WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result['password'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Returns the mojang uuid affiliated with
	 * the uid of the user
	 */
	public function fetchMUuidFromUid($uid) {
		try {
			$query = $this->pdo->prepare('SELECT m_uuid FROM auth_users WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if($result) {
				return $result['m_uuid'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Returns the m_uuid affiliated with the token,
	 * otherwise, just returns false for no token.
	 */
	public function fetchMUuid($token) {
		try {
			$query = $this->pdo->prepare('SELECT m_uuid FROM auth_registrationtokens WHERE token = :token');
			$query->bindParam(':token', $token);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($result) {
				return $result['m_uuid'];
			}
			return false;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Calls to remove the reset token
	 */
	public function removeResetTokens($uid) {
		try {
			$query = $this->pdo->prepare('DELETE FROM auth_resettokens WHERE uid = :uid');
			$query->bindParam(':uid', $uid);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Calls to remove the token from the database 
	 * affiliated with the token data
	 */
	public function removeAuthToken($token) {
		try {
			$query = $this->pdo->prepare('DELETE FROM auth_tokens WHERE token = :token');
			$query->bindParam(':token', $token);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/* Calls to remove the m_uuid affiliated token
	 * from auth_tokens
	 */
	public function removeRegistrationToken($token) {
		try {
			$query = $this->pdo->prepare('DELETE FROM auth_registrationtokens WHERE token = :token');
			$query->bindParam(':token', $token);
			$query->execute();
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	public function setup() {
		try {
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_tokens(uid VARCHAR(32), token VARCHAR(64), time INTEGER(12), UNIQUE KEY(token))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_users(uid VARCHAR(32), m_uuid VARCHAR(32), username VARCHAR(32), password VARCHAR(60), email VARCHAR(128), verified BOOLEAN, group_level INTEGER(4), UNIQUE KEY(uid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_emailtokens(uid VARCHAR(32), email VARCHAR(64), email_token VARCHAR(16), time INTEGER(12), UNIQUE KEY(uid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_registrationtokens(token VARCHAR(16), m_uuid VARCHAR(32), time INTEGER(12), UNIQUE KEY(m_uuid))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_apitokens(access_token VARCHAR(32), client_token VARCHAR(128), uid VARCHAR(32), time INTEGER(12), UNIQUE KEY(access_token))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_groups(group_level INTEGER(4), group_name VARCHAR(32), level_ingame INTEGER(4), level_irc INTEGER(4), level_logs BOOLEAN, UNIQUE KEY(group_level))");
			$query = $this->pdo->query("CREATE TABLE IF NOT EXISTS auth_resettokens(uid VARCHAR(32), token VARCHAR(16), time INTEGER(12), UNIQUE KEY(token))");
			return "Successfully setup the database.";
		} catch (PDOException $e) {
			return "Error with setting up database\n" . $e->getMessage();
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
	error_log($e->getMessage());
	die("Database initialization error");
}
?>
