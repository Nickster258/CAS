<?php
if (!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}
// Database
define('DBHOST', "localhost");
define('DBUSER', "dbuser");
define('DBPASS', "dbpass");
define('DBNAME', "databasename");
// Email
define('EMAIL_USERNAME', "username");
define('EMAIL_PASSWORD', "password");
define('EMAIL_HOST', "host");
define('EMAIL_PORT', 465);
define('EMAIL_SENDER', "email@example.com");
define('EMAIL_SENDER_NAME', "Example Name");
// General
<<<<<<< HEAD
define('ROOT_DIR', "/root/directory/of/server/");
=======
>>>>>>> c294ee94b92a2838729376368c49a1a83d907a3e
define('SERVICE_NAME', "Service Name");
define('URL', "https://sub.example.com/");
define('DOMAIN', "example.com");
?>
