<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="UBC Airline Booking Service">
	<title>UBC Air</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
	<link rel="stylesheet" href="css/mainpage.css"
</head>

<body>
<div class="header">
    <div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="">UBC Air</a>
        <ul>
            <li><a href="index.php">Home</a></li>
        </ul>
    </div>
</div>

	<div class="banner">
    <h1 class="banner-head">
        Welcome to UBC Air
    </h1>
</div>

You are now logged out. Please wait while we redirect you to the home page.

</body>

<?php
	if(array_key_exists('loggedin', $_COOKIE)) {
		// delete cookie
		setcookie('loggedin', null, 1);		
		setcookie('cid', null, 1);
		setcookie('cname', null, 1);
		// Redirect to index
		header('Refresh: 3; index.php');	
		
	}
