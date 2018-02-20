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
	overflow: hidden;
}
* {
	font-family:Verdana;
	background-color: #E5E8EA;
	text-align: center;
	color: #443D35
}
</style>
<link rel="icon" href="favi.png"/>
<title>Simple Shortener</title>
</head>
<body>
<div class="outer">
	<div class="middle">
		<div class="inner">
			<?php
				session_start();
				if(isset($_SESSION["uuid"]) && isset($_SESSION["token"])) {
	
					echo "<p>Register</br></br>
					<form action=\"register.php\" method=\"post\">
					UUID: " . $_SESSION["uuid"] . "
					Name: <input type=\"text\" name=\"name\"><br>
					Email: <input type=\"email\" name=\"email\"><br>
					Password: <input type=\"password\" name=\"pass\"><br>
					Verify password: <input type=\"password\" name=\"verifiedpass\"><br>
					<input type=\"submit\">
					</form>
					</p>";
				} else {
					echo "<p>Login</br></br>
					<form action=\"login.php\" method=\"post\">
					Email: <input type=\"email\" name=\"email\"><br>
					Password: <input type=\"password\" name=\"pass\"><br>
					<input type=\"submit\">
					</form>
					</p>";
				}
			?>
		</div>
	</div>
</div>
</body>
</html>
