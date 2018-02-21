<?php
require __DIR__ . '/constants.php';

try {
	$handle = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $dbuser, $dbpass);
	$handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo $e->getMessage();
	die();
}
?>
