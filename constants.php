<?php
if (!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

define('DBHOST', "localhost");
define('DBUSER', "dbuser");
define('DBPASS', "dbpass");
define('DBNAME', "databasename");

define('EMAIL_TOKEN_LENGTH', 16);
define('URL', "https://sub.example.com/");
define('DOMAIN', "example.com");
?>
