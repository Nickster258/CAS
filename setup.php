<?php
require_once 'constants.php';
require_once 'database.php';

try {
	$query = $handle->query("CREATE TABLE IF NOT EXISTS auth_users(uid VARCHAR(16), m_uuid VARCHAR(32), username VARCHAR(32), password VARCHAR(60), salt VARCHAR(16), email VARCHAR(64), verified BOOLEAN, UNIQUE KEY(uid))");
	$query = $handle->query("CREATE TABLE IF NOT EXISTS auth_emailtokens(uid VARCHAR(16), email VARCHAR(64), email_token VARCHAR(16), UNIQUE KEY(uid))");
	$query = $handle->query("CREATE TABLE IF NOT EXISTS auth_tokens(token VARCHAR(16), m_uuid VARCHAR(32), time INT, UNIQUE KEY(m_uuid))");
} catch (PDOException $e) {
	echo  '<pre>' . $e->getMessage() . '</pre>';
	die();
}
?>
