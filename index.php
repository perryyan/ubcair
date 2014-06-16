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
<?php

	if(!array_key_exists('loggedin', $_COOKIE) ) {
		echo "<li><a href='login.php'>Login</a></li>";
		echo "<li><a href='register.php'>Sign Up</a></li>";
	}
	else {
		echo "<li><a href='logout.php'>Logout</a></li>";
		echo "<li><a href='support.php'>My Orders</a></li>";
	}   
?>    
			<li><a href="flights.php">Find flights</a></li>   
        </ul>
    </div>
</div>

	<div class="banner">
    <h1 class="banner-head">
        Welcome to UBC Air
    </h1>
</div>

<div class="content-wrapper">
    <div class="content">
        <h2 class="content-head is-center">Welcome to UBC Air - buy cheap flight tickets online.</h2>
            <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
                <h3 class="content-subhead">
                    Find your dream vacation here!
                </h3>
            </div>
    </div>


    <div class="content">
            <div class="l-box-lrg pure-u-1 pure-u-md-3-5">
                <h4>Contact Us</h4>
                <p>Contact the system administrator at perry [at] perryyan.com</p>
            </div>
        </div>

    </div>

    <div class="footer l-box is-center">
		Notice: The contents and information from this web site are ficticious and is meant for educational purposes only. Copyright UBC CPSC 2014.
    </div>

</div>

</body>