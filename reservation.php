<<<<<<< HEAD
<!--Script for toggling flight details-->
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
	$(document).ready(function(){
    	$(".toggler").click(function(e){
        	e.preventDefault();
       		$('.detail'+$(this).attr('detail-num')).toggle();
    	});
	});
</script>

=======
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
>>>>>>> 67d46c7d238529e16d4d7c41e1961d267a6cfa0a
<?php

// These stuff are needed (for now) to connect Oracle, will figure out how to import from
// main php file
$success = True; //keep track of errors so it redirects the page only if there are no errors
	
//include "flightdetails.php";
include "oci_functions.php";
include "regexhelper.php";

if ($db_conn) {
 	if (array_key_exists('flightchoice', $_POST)) {
 		// test YVR to TPE (country TW) in flights.php it has 1 and 2 transfers
 		// direct flight I always test with YVR to HKG (country HK) but doesnt matter
<<<<<<< HEAD
		$route = unserialize($_POST['flightchoice']);
		print_r($route);
		printDetails($route, 0);
=======
		$flight = unserialize($_POST['flightchoice']);
		print_r($flight);
>>>>>>> 67d46c7d238529e16d4d7c41e1961d267a6cfa0a
	}
}

// Function to parse flight.
// Returns an array with the flight tuple and number of transfers
/*
function parseFlight($flight) {
		
		$flight_tuple = explode(' ', $flight);
		
		// Indexes
		$num_transfer = 0;
		$num_airport = 0;
		$num_

		while (list($key, $value) = each($flight_tuple)) {
			
			// Counts the number of transfers
			if(match_5_digits($value)) {
				$parsed_flight['flight'.$num_transfer] = $value;
				$num_transfer++;
			}
			
			// Airports. Will always be in order, so AP0 -> AP1 -> AP2 etc.
			if(match_airport_format($value)) {
				$parsed_flight['ap'.$num_airport] = $value;
				$num_airport++;
			}
			
			if(match_yymmdd_format($value)) {
				
			}
			
		}
		
		
		$parsed_flight['num_transfer'] = $num_transfer;
		
		return $flight_tuple;
		
}*/
 
?>