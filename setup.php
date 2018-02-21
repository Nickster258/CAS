<?php
require __DIR__ . '/constants.php';

$conn = mysql_connect($dbhost, $dbuser, $dbpass);

mysql_select_db($db, $conn);

if(!$conn) {
	die(mysql_error());
	echo "hi";
} else {
	global $conn;
	$query = "CREATE TABLE IF NOT EXISTS auth_users(uid VARCHAR(16), m_uuid VARCHAR(32), username VARCHAR(32), password VARCHAR(60), salt VARCHAR(16), email VARCHAR(64), verified BOOLEAN)";
	$result = mysql_query($query, $conn);
	$query = "CREATE TABLE IF NOT EXISTS auth_emailtokens(uid VARCHAR(16), email VARCHAR(64), email_token VARCHAR(16))";
	$result = mysql_query($query, $conn) or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
    foreach($row as $cname => $cvalue){
        print "$cname: $cvalue\t";
    }
    print "\r\n";
}
}
?>
