<?php

define("IN_CAS", true);

require_once 'constants.php';
require_once 'database.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

print_r($handler->setup());
?>
