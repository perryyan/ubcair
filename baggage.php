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

<form action ="baggage.php" method = "POST">
Please select the number of baggages:
<input type="number" id="numbaggages" name="numbaggages" min="0" max="3" value="0">
<br>
<button type="submit" class="pure-button pure-button-primary" name="insertsubmit">Submit number of baggage(s)</button>
<button name="reset">Reset</button>
</form>


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>


</script>
<?php

$success = True; //keep track of errors so it redirects the page only if there are no errors
//$db_conn = OCILogon("ora_c2e8", "a42375105", "ug");

include "oci_functions.php";
$numbaggages = $_POST['numbaggages'];
//$baggage = array("0", "0", "0");
$mycid = $_COOKIE['cid'];
$temp;

function printInsertFields($numbaggages) {
	//echo "I am printing insert fields<br>";
	?>
	<form method='POST' action='baggage.php'>
		<table>
	<?php
	for ($it=0; $it < $numbaggages; $it++) {
		echo "Enter the weight of bag ".number_format($it+1).":
			<input type='number' name='weight$it' id='baggages' step='any' style='width:60px'>Kg<br>";
	} 
	?>
		</table>
		<button type="submit" name="finalsubmit">Submit</button>
	</form>
	<?php

}

// Connect Oracle...
if ($db_conn) {
		
	if(array_key_exists('insertsubmit', $_POST)){
			$numbaggages = $_POST['numbaggages'];
			setcookie('numbaggages',$numbaggages);	
		//echo "I am in insertsubmit<br>";
			printInsertFields($_COOKIE['numbaggages']);
	}	
	
	
	if(array_key_exists('finalsubmit', $_POST)){
			//echo "I am in finalsubmit<br>";
			
			
			for($i=0; $i<$_COOKIE['numbaggages'];$i++)
			{
			//	echo "For".$i;			
				//Getting the values from user and insert data into the table
				//set default status to checked in	
				executePlainSQL
				("
					insert into has_B values 
							(
								".rand(0,999999).",		
								".$_COOKIE['cid'].",	
								3,
								".$_POST['weight'.$i].",
								'".date('Y-m-d H:i:s', time())."'
							)
				");
				OCICommit($db_conn);
				
				?>
				<script>
					alert("Bag add successful.");
					location = "support.php";
			</script>
			<?php
			
		}
	}
	
	/*if (array_key_exists('reset', $_POST)) {
			// Drop old table...
			echo "<br> dropping table <br>";
			executePlainSQL("drop table Baggage");
			//executePlainSQL("drop sequence bid_sequence");
			
			echo "<br> Creating Baggage table <br>";
			//get cid: cid number references Customer(cid),
			
			$q = "create table Baggage(
				bid number(10,0) PRIMARY KEY,
				cid	number(9,0),
				weight number(10,0),
				status char(16)
				FOREIGN KEY cid references Customer(cid))";
	
			$stmt = oci_parse($db_conn, $q);
			$r = oci_execute($stmt, OCI_DEFAULT);

			oci_free_statement($stmt);
			OCIcommit($db_conn);
	
	}*/

	if ($_POST && $success) {
		echo "<script>document.getElementById('numbaggages').value='".$_COOKIE['numbaggages']."'</script>";
	}
	/*else {
		echo "<script>document.getElementById('numbaggages').value='".$_COOKIE['numbaggages']."'</script>";
		$result = executePlainSQL("select * from Baggage");
			printResult($result);
	}*/
					
	/*
			// Create sequence
			executePlainSQL("create sequence bid_sequence 
							start with 0 
							increment by 1 
							minvalue 0
							maxvalue 1000");
	*/
	
	/*
		if (array_key_exists('updatesubmit', $_POST)) {
			$tablename = "Baggage"; 
			$newWeight = $_POST['newWeight'];
			$newStatus = $_POST['newStatus'];
			$searchedvalue = $_POST['updateBid'];
			if($newWeight !="" && $newStatus != "")
			{
				executePlainSQL("update ".$tablename."
					 set weight = '".$newWeight."' 
					 set status = '".$newStatus."' 
					 where bid = '".$searchedvalue."'");
			}
			else if($newWeight != "")
			{
				executePlainSQL("update ".$tablename."
					set weight = '".$newWeight."' 
					where bid = '".$searchedvalue."'");
			}
			else if($newStatus != "")
			{
				executePlainSQL("update ".$tablename."
					set status = '".$newStatus."' 
					where bid = '".$searchedvalue."'");
			}
			*/
			// Select data...
	}

?>
</div>
</body>