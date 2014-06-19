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
            <li><a href="flights.php">Find flights</a></li>      
        </ul>
    </div>
</div>
        </ul>
    </div>
</div>

	<div class="banner">
    <h1 class="banner-head">
        Welcome to UBC Air
    </h1>
</div>

<body>
<div class = "content-customer-area">

Status of my bags: <br>
<?php
include 'oci_functions.php';

function parseStatus($code) {
		
	$out = "";	
		
	switch($code) {
		case 0: $out = "In transit";
				break;
		
		case 1: $out = "Lost";
				break;
		
		case 2: $out = "Picked up";
				break;
		
		case 3: $out = "Checked in";
				break;
		}
		return $out;
}
		

if($db_conn) {
		
		$q = "select bid, status, weight_kg, last_update from has_B where cid = '".$_COOKIE['cid']."'";	
		$options = executePlainSQL($q);

		echo "<table class='pure-table pure-table-bordered'>
			<tr>
				<thead>
				<td>bid</td>
				<td>status</td>
				<td>weight_kg</td>
				<td>last_update</td>
				</thead>
			</tr>";
		
		while($row = oci_fetch_array($options, OCI_BOTH)) {
			echo "<tr>".
					"<td>".$row[0]."</td>".
				  	"<td>".parseStatus($row[1])."</td>".
				  	"<td>".$row[2]."</td>".
				  	"<td>".$row[3]."</td>".
				  "</tr>";
		}
		echo "</table>";
		
	}
?>
</div>
</body>