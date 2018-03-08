<html>
<head>
<meta name="viewport" content="width=device-width">
<style>
.outer {
	display: table;
	position: relative;
	height: 100%;
	width: 100%;
}
.middle {
	display: table-cell;
	vertical-align: middle;
}
.inner {
	margin-left: auto;
	margin-right: auto;
	width: 100%;
	max-width: 350px;
	overflow: hidden;
	font-size: 26px;
}
* {
	font-family:Verdana;
	background-color: #E5E8EA;
	text-align: center;
	color: #443D35
}
a {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
	color: #821200;
}
.button {
	background-color: #a71700;
	border: none;
	color: white;
	padding: 10px 16px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	margin: 4px 2px;
	cursor: pointer;
	margin-top: 16px;
	width: 60%;
}
.button:hover {
	background-color: #821200;
}
.input {
	background-color: white;
	border: none;
	color: black;
	padding: 10px 16px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	margin: 4px 2px;
	border: 1px solid #a71700;
	border-radius: 2px;
	width:320px;
	margin-bottom:20px;
}
.linkFormat {
	text-decoration: none;
	color: #4CAF50;
}
p {
	font-size: 20px;
	text-align: left;
}
.input_style {
	width: 320px;
	font-size: 12px;
	text-align: left;
	padding-left: 20px;
}
.user_content {
	width: 100%;
	font-size: 11px;
	padding-top: 5px;
}
.bold {
	font-weight: bold;
}
.title {
	margin-bottom: 10px;
}
.subtitle {
	margin-bottom: 10px;
	font-size: 16;
}
.welcomeback {
	font-size: 12;
	margin-bottom: 20px;
}
.logout {
	font-size: 11;
	margin-bottom: 20px;
}
.name {
	color: #a71700;
}
#footer {
	padding-top: 10px;
	font-size: 10px;
}
.failure {
	font-size: 14px;
	padding-top: 10px;
	color: red;
}
.success {
	font-size: 14px;
	padding-top: 10px;
	color: green;
}
.neutral {
	font-size: 10px;
	padding-top: 10px;
}
</style>
<link rel="icon" href="favi.png"/>
<title>CAS</title>
</head>
<body>
<?php

define ('IN_CAS', true);

require_once 'constants.php';
require_once 'database.php';
require_once 'response.php';
require_once 'utilities.php';

global $pdo;

$handler = new DatabaseHandler($pdo);

session_start();

verify_login();

if(!isset($_SESSION["uid"])) {
	$response = new UserResponse("notAuthorized");
	$response->redirect();
}

do_response("generic");

echo "<div class=\"outer\"><div class=\"middle\"><div class=\"inner\"><div class=\"title\"><span class=\"bold\">C</span>AS</div>";
				function print_footer() {
					echo "<hr><div id=\"footer\"><a href=\"" . URL . "terms.php\">Terms</a> | <a href=\"https://github.com/Nickster258/CAS\">Source</a> | Contact help</div>";
				}

				if (isset($_SESSION["uid"])) {

					if (!isset($_SESSION["m_uuid"])) {
						$_SESSION["m_uuid"] = $handler->fetchMUuidFromUid($_SESSION["uid"]);
					}

					echo "<div class=\"subtitle\"><span class=\"bold\">U</span>ser</div>
					<div class=\"input_style\">Mojang UUID</div>
					<div class=\"user_content\">" . $_SESSION["m_uuid"] . "</div> 
					<div class=\"input_style\">Name</div>
					<div class=\"user_content\">" . $handler->fetchNameFromUid($_SESSION["uid"]) . "</div>
					<div class=\"input_style\">Email</div>
					<div class=\"user_content\">" . $handler->fetchEmailFromUid($_SESSION["uid"]) . "</div>
					<hr>
					<div class=\"subtitle\"><span class=\"bold\">R</span>eset <span class=\"bold\">P</span>assword</div>
					<form action=\"login.php\" method=\"post\">
					<div class=\"input_style\">Old Password</div> <input class=\"input\" type=\"password\" name=\"old_pass\" required><br>
					<div class=\"input_style\">New Password</div> <input class=\"input\" type=\"password\" name=\"new_pass\" required><br>
					<div class=\"input_style\">Verify New Password</div> <input class=\"input\" type=\"password\" name=\"new_pass_verify\" required><br>";

					do_response("settings_form");

					echo "<input class=\"button\" type=\"submit\" value=\"Reset\">
					</form>
					</p>";
					print_footer();
				}
			?>
		</div>
	</div>
</div>
</body>
</html>