<?php

include('oci_functions.php');
?>
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
            <li><a href='index.php'>Home</a></li>
			<li><a href='login.php'>Login</a></li>
            <li><a href="register.php">Sign Up</a></li>    
        </ul>
    </div>
</div>

	<div class="banner">
    <h1 class="banner-head">
        Welcome to UBC Air
    </h1>
</div>

<?php

echo "email = ".$_GET['email'];


	echo "<div class='content-customer-area'>";
	echo "<p>Welcome, ".$_GET['cname']
				."! You are customer id #".$_GET['cid']
				.". Your email address is ".$_GET['email'];
	echo "<br>This is the customer support area. Please select your action from the menu above</p></div>";	
?>
</body>