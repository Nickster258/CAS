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
	color: #4CAF50;
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
	padding-top: 10px;
	padding-left: 20px;
}
.uuid_style {
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
				if(isset($_SESSION["m_uuid"]) && isset($_SESSION["token"])) {
	
					echo "<div class=\"title\"><span class=\"bold\">R</span>egister</div>
					<div class=\"input_style\">Mojang UUID</div>
					<div class=\"uuid_style\">" . $_SESSION["m_uuid"] . "</div> 
					<form action=\"register.php\" method=\"post\">
					<div class=\"input_style\">Name</div> <input class=\"input\" type=\"text\" name=\"name\" placeholder=\"LordDecrapo\" required><br>
					<div class=\"input_style\">Email</div> <input class=\"input\" type=\"email\" name=\"email\" placeholder=\"capois@dumb.com\" required><br>
					<div class=\"input_style\">Password</div> <input class=\"input\" type=\"password\" name=\"pass\" required><br>
					<div class=\"input_style\">Verify Password</div> <input class=\"input\" type=\"password\" name=\"verified_pass\" required><br>
					<input class=\"button\" type=\"submit\" value=\"Register\">
					</form>
					</p>";
				} else {
					echo "<p>Login</br></br>
					<form action=\"login.php\" method=\"post\">
					<div class=\"input_style\">Email</div> <input class=\"input\" type=\"email\" name=\"email\" required><br>
					<div class=\"input_style\">Password</div> <input class=\"input\" type=\"password\" name=\"pass\" required><br>
					<input class=\"button\" type=\"submit\" value=\"Login\">
					</form>
					</p>";
				}
			?>
		</div>
	</div>
</div>
</body>
</html>
