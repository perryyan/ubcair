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
<?php

	if(!array_key_exists('loggedin', $_COOKIE) ) {
		echo "<li><a href='login.php'>Login</a></li>";
		echo "<li><a href='register.php'>Sign Up</a></li>";
		header('location: login.php');
	}
	else {
		echo "<li><a href='logout.php'>Logout</a></li>";
		echo "<li><a href='support.php'>My Orders</a></li>";		
	}   
?>    
            <li><a href='flights.php'>Find flights</a></li>
        </ul>
    </div>
</div>

	<div class="banner">
    <h1 class="banner-head">
        Welcome to UBC Air
    </h1>
</div>


<body>
<div class="content-customer-area">
<?php

echo "Welcome " . $_COOKIE["cname"] . "!<br>";

if($db_conn) {
	$q = "select is_admin from Customer where cid=".$_COOKIE['cid'];
	$stmt = oci_parse($db_conn,$q);
	$r = oci_execute($stmt, OCI_DEFAULT);
	
	$row = oci_fetch_array($stmt, OCI_BOTH);
	if($row['IS_ADMIN'] == 1) {
		echo "<a href='admin.php'>Administrative console</a><br>";		
	}
	OCILogoff($db_conn);
}
?>

<a href='profile.php'>Edit my profile</a><br>
<a href='list_reservation.php'>My reservations</a><br>
<a href='list_baggage.php'>My baggage</a><br>

<a href='statistics.php'>Statistics of the site</a>

</div>
</body>