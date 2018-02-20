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
			<p>Login</br></br>
				<form action=\"register.php\" method=\"post\">
					Name: <input type=\"text\" name=\"name\"><br>
					Password: <input type=\"password\" name=\"pass\"><br>
					<input type=\"submit\">
				</form>
			</p>
		</div>
	</div>
</div>
</body>
</html>