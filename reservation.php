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
	
	if (array_key_exists('flightchoice', $_POST)) {
 		// test YVR to TPE (country TW) in flights.php it has 1 and 2 transfers
 		// direct flight I always test with YVR to HKG (country HK) but doesnt matter
		$res = unserialize($_POST['flightchoice']);
		//print_r($res);
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
<div class='content-customer-area'><body>
<?php

// These stuff are needed (for now) to connect Oracle, will figure out how to import from
// main php file
$success = True; //keep track of errors so it redirects the page only if there are no errors
	
//include "flightdetails.php";
include "oci_functions.php";

if ($db_conn) {
	
	if (array_key_exists('makeres', $_POST)) {
		
		// Insert reservation tuple into make_res
		// (resid, cid, pclass, ticket_num) 
		executePlainSQL("insert into make_res values ("
						.$_POST['makeres_resid'].","
						.$_COOKIE['cid'].","
						.$_POST['makeres_classint'].","
						.$_POST['makeres_numtickets'].")");
		OCICommit($db_conn);			
		
		// res_includes(fid, resid, resorder)
		$fids = array($_POST['fid1'], $_POST['fid2'], $_POST['fid3']);

		foreach($fids as $key=>$value) {
			//echo $fids[$key]."<br>";
			if( $fids[$key] != "") {
				executePlainSQL("insert into res_includes values ("
					.$value.","
					.$_POST['makeres_resid'].","
					.number_format($key+1).")");
			}
		}

		OCICommit($db_conn);
		
		executePlainSQL("insert into payment values("
				.$_POST['makeres_payid'].","
				.$_POST['credit'].","
				.$_COOKIE['cid'].")");	
				
		OCICommit($db_conn);		
		
		executePlainSQL("insert into deter_pay values("
				.$_POST['makeres_payid'].","
				.$_POST['makeres_resid'].","
				.$_POST['makeres_totalcost'].")");
		
		OCICommit($db_conn);
		
		// Confirmation dialog
		?>
		<script type="text/javascript"> 
			alert("Payment successful.");
			location = "support.php";
		</script>
		<?php
	}
	
	else {
		//print_r($res);
		echo "Your flight itinerary:";
		printDetails($res, 0, 0);	
		
		?>
		<br><br>The summary of your ticket order:
		<table class="pure-table">
			<tr>
				<?php 
				echo "<td>Number of tickets:</td>";
				echo "<td>".$res['NUMTICKETS']."</td>"; 
				?>
			</tr>
			<tr>
				<?php 
				echo "<td>Flight class:</td>";
				echo "<td>".$res['CLASS']."</td>"; 
				?>
			</tr>
			<tr>
				<?php 
				echo "<td>Price per ticket:</td>";
				echo "<td>CAD$".number_format($res['TOTALPRICE'],2)."</td>";
				?>
			</tr>
			<tr>
				<?php
					$tcost = floatval($res['NUMTICKETS']) * floatval($res['TOTALPRICE']);
					echo "<td>Total cost:</td>";
					echo "<td>CAD$".number_format($tcost,2)."</td>";
				?>
			</tr>
		</table> 
		<?php
	}
	

}

?>
		<form class="pure-form" method="POST" action="reservation.php">
			<input type="hidden" name="makeres_totalcost" value="<?php echo $tcost; ?>">
			<input type="hidden" name="makeres_classint" value="<?php echo $res['CLASSINT']; ?>"> 
			<input type="hidden" name="makeres_numtickets" value="<?php echo $res['NUMTICKETS']; ?>"> 
			<input type="hidden" name="makeres_resid" value="<?php echo rand(10000000,99999999); ?>"> 
			<input type="hidden" name="makeres_payid" value="<?php echo rand(10000000,99999999); ?>">
			<input type="hidden" name="fid1" value="<?php echo $res['FIRSTID']; ?>"> 	
			<input type="hidden" name="fid2" value="<?php echo $res['SECONDID']; ?>"> 	
			<input type="hidden" name="fid3" value="<?php echo $res['THIRDID']; ?>"> 	
			<input type="number" placeholder="Credit Card Number" name="credit" id="credit" required>											
		    <button type="submit" name="makeres" class="pure-button pure-button-primary">Pay now</button>
	    </form>
</div></body> 