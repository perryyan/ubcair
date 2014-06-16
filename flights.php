
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
<div class='content-customer-area'>
	
<p>Search for your perfect flight<br></p>
<p><a href="http://www.textfixer.com/resources/dropdowns/country-list-iso-codes.txt" target="_blank">Country Codes</a><br></p>
<!--Drop down list frames for choosing depart/arrive location, contents will be dynamically created-->
<form method="POST" action="flights.php"><table>
<tr>
<td>Departure location:</td>
<td><select id="depcountry" name="depcountry" onchange="this.form.submit()">
		<option selected value = "default">(Choose country)</option>		
	</select>
	<noscript><input type="submit" value="Submit"></noscript>
</td>
<td><select id="depcity" name="depcity">
	<option selected value = "default">(Choose city)</option>	
	</select>
</td>
</tr>
<tr>
<td>Arrival location:</td>
<td><select id="descountry" name="descountry" onchange="this.form.submit()">
		<option selected value = "default">(Choose country)</option>	
	</select>
	<noscript><input type="submit" value="Submit"></noscript>
</td>
<td><select id="descity" name="descity">
		<option selected value = "default">(Choose city)</option>	
	</select>
</td>
</tr>


<?php
$earliestdate = date("Y-m-d");
echo "<tr><td>Earliest Date</td><td><input type='date' name='flightdate' id='flightdate' value='".$earliestdate."'</td></tr>";
?>
<tr>
<td>Number of transfers: </td>
<td><input type="radio" checked name="maxnumtrans" value="1">0-1</td>
<td><input type="radio" name="maxnumtrans" id="maxnumtransinf" value="inf">2+</td>
</tr>
<tr>
<td>Enter number of tickets: </td>
<td><input type="number" name="numtickets" id="numtickets" min="1" value="1" style="width:60px" required></td>
</tr>
<tr>
<td>Preferred class:</td>
<td>
<select name="flightclass" id="flightclass">
	<option value="economy">Economy class</option>
	<option value="business">Business class</option>
	<option value="first">First class</option>
</select>
</td>
</tr>

<tr><td><input type="submit" value="Submit" name="searchsubmit"></td>
	<td><input type="submit" value="Clear all" name="clearsubmit"></td>
</table>
</form>

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
<?php
	
// These stuff are needed (for now) to connect Oracle, will figure out how to import from
// main php file
$success = True; //keep track of errors so it redirects the page only if there are no errors
//$db_conn = OCILogon("ora_b4s8", "a16894123", "ug");
// Functions for interacting with Oracle DBMS	
include "oci_functions.php";

// To populate the dropdown frames in the HTML part above with options
function printoptions($options, $dropdownid) {
	$it = 1;
	while ($option = OCI_Fetch_Array($options, OCI_NUM)) {
		echo "<script>var c = document.createElement('option');";
		echo "c.value = '$option[0]';";
		echo "c.text = '$option[0]';";
		echo "document.getElementById('$dropdownid').options.add(c, $it)</script>";
		$it++;
	}
}

// Prints the flight search results as a table with show details button and button to select the flight
// for booking		
function printFlights($flights, $locations) {
	echo "<p><br>Search Results: <br></p>";
	echo "<form method='POST' action='reservation.php'>";
	echo '<table class = "pure-table pure-table-bordered">';
	// print the top row (attribute labels)
	echo '<thead>';
	echo "<tr><th>Departure Airport</th><th>City</th><th>Country</th>"
			."<th>Arrival Airport</th><th>City</th><th>Country</th><th>Departure Time (GMT)</th>"
	    	."<th>Total Flight Time</th><th>COST (CAD)</th><th>Choose Flight</th></tr>";
	echo '</thead>';
	// print the data rows (tuples)
	$it = 0;
	while ($flight = OCI_Fetch_Array($flights, OCI_ASSOC)) {
		$printout = "<tr>";
		foreach($locations as $value) {
			$printout = $printout . "<td>$value</td>";
		}
		$departtime = parseDate($flight['DT1'], 1);
		$flighttime = parseDate($flight['TOTALTIME'],2);
		$cost = $flight['TOTALPRICE'];
		
		// Variable costs depends on class
		if(strcmp($_COOKIE['flightclass'], "economy") == 0 ) {
			$cost *= 1;
			$fclassint = 0;
		}
		else if (strcmp($_COOKIE['flightclass'], "business") == 0) {
			$cost *= 3;
			$fclassint = 1;
		}
		
		else if (strcmp($_COOKIE['flightclass'], "first") == 0 ) {
			$cost *= 5;
			$fclassint = 2;
		}
		// update the cost if the class is changed
		$flight['TOTALPRICE'] = $cost;  
		
		// Add class and num tickets to the post array
		$flight['CLASS'] = $_COOKIE['flightclass'];
		$flight['CLASSINT'] = $fclassint;
		
		$flight['NUMTICKETS'] = $_COOKIE['numtickets'];
			
		$flight_string = serialize($flight);
		echo $printout . "<td>$departtime</td><td>$flighttime</td><td>$cost</td>";
		echo "<td><input type='radio' name='flightchoice' value='$flight_string' required></td></tr>";
		echo "<tr><td>";
		printDetails($flight, $it, 1);
		echo "</td></tr>";
		$it++;
	}		
	echo "</table></div>";
	echo "<input type='submit' value='Book my flight'></form>";
}

if ($db_conn) {
// Detecting form submission (the dropdown lists for flight searching) and set cookie for processing
// at next load
 	if (array_key_exists('depcountry', $_POST) && (strcmp($_POST['depcountry'],"default") !== 0)) {
		setcookie('depcountry',$_POST['depcountry']);
	}
	if (array_key_exists('descountry', $_POST) && (strcmp($_POST['descountry'],"default") !== 0)) {
		setcookie('descountry',$_POST['descountry']);
	}
	if (array_key_exists('depcity', $_POST) && (strcmp($_POST['depcity'],"default") !== 0)) {
		setcookie('depcity',$_POST['depcity']);
	}
	if (array_key_exists('descity', $_POST) && (strcmp($_POST['descity'],"default") !== 0)) {
		setcookie('descity',$_POST['descity']);
	}
	if (array_key_exists('maxnumtrans', $_POST)) {
		setcookie('maxnumtrans',$_POST['maxnumtrans']);
	}
	if (array_key_exists('flightdate', $_POST)) {
		setcookie('flightdate', $_POST['flightdate']);
	}
	if (array_key_exists('numtickets', $_POST)) {
		setcookie('numtickets', $_POST['numtickets']);
	}
	if (array_key_exists('flightclass', $_POST)) {
		setcookie('flightclass', $_POST['flightclass']);
	}	
	if (array_key_exists('clearsubmit', $_POST)) {
		setcookie('depcountry',"",time() -3600);
		setcookie('descountry',"",time() -3600);
		setcookie('depcity',"",time() -3600);
		setcookie('descity',"",time() -3600);
		setcookie('maxnumtrans',"",time() -3600);
		setcookie('flightdate', "",time() -3600);
		setcookie('numtickets',"",time()-3600);
		setcookie('flightclass',"",time()-3600);
	}

	
	if ($_POST && $success) {
		header("location: flights.php");
	}
// Now retrieve user input data from cookies, process them and make queries to database
// Note: codes below will be run at page start because the $_POST does not exist at that time,
// these will also be run right after setting the cookies above (after user form submission),
// because of the header("location: flights.php") will reload the page, that is why we need the 
// cookies. 
// Reason why we need to reload the page after submit is because .... well it's in the sample...
	else {
		$depcity; 
		$descity; 
		$flightclass; 
		$numtickets;
		$flightdate = date("Y-m-d");
		$depcountries = executePlainSQL("select distinct A.country" 
	 								   	." from Flight F, Airport A"
	 									." where F.departap = A.code"
										." order by country");
		printoptions($depcountries, "depcountry");
			
		$descountries = executePlainSQL("select distinct A.country" 
	 								   	." from Flight F, Airport A"
	 									." where F.arrivalap = A.code"
										." order by country");
		printoptions($descountries, "descountry");
				
		if (array_key_exists('depcountry', $_COOKIE)) {
			$depcountry = $_COOKIE['depcountry'];	
			echo "<script>document.getElementById('depcountry').value='$depcountry'</script>";
			$depcities = executePlainSQL("select distinct A.city" 
	 								   	." from Flight F, Airport A"
	 									." where F.departap = A.code"
	 									." AND A.country='$depcountry'"
										." order by city");
			printoptions($depcities, "depcity");
		}
		if (array_key_exists('descountry', $_COOKIE)) {
			$descountry = $_COOKIE['descountry'];
			echo "<script>document.getElementById('descountry').value='$descountry'</script>";
			$descities = executePlainSQL("select distinct A.city" 
	 								   ." from Flight F, Airport A"
	 								   ." where F.arrivalap = A.code AND A.country='$descountry'"
									   ." order by city");
			printoptions($descities, "descity");
		}
		if (array_key_exists('depcity', $_COOKIE)) {
			$depcity = $_COOKIE['depcity'];
			echo "<script>document.getElementById('depcity').value='$depcity'</script>";	
		}
		if (array_key_exists('descity', $_COOKIE)) {
			$descity = $_COOKIE['descity'];
			echo "<script>document.getElementById('descity').value='$descity'</script>";	
		}
		if (array_key_exists('flightdate', $_COOKIE)) {
			$flightdate = $_COOKIE['flightdate'];
			echo "<script>document.getElementById('flightdate').value='$flightdate'</script>";
		}
		if (array_key_exists('numtickets', $_COOKIE)) {
			$numtickets = $_COOKIE['numtickets'];
			echo "<script>document.getElementById('numtickets').value='$numtickets'</script>";
		}
		if (array_key_exists('flightclass', $_COOKIE)) {
			$flightclass = $_COOKIE['flightclass'];
			echo "<script>document.getElementById('flightclass').value='$flightclass'</script>";
		}		
// The above set the drop down lists according to the cookies, will not work if we don't reload
// the page as done by header("location:flights.php")
// The below do the magical/legendary/highly-inefficient search query to Oracle for retrieving flight
// data according to user's search criteria 
		if (strcmp($depcity,"") !== 0 && strcmp($descity,"") !== 0) {
			$departap = oci_fetch_row(executePlainSQL("select code from Airport where city='$depcity' AND country='$depcountry'"));
			$arrivalap = oci_fetch_row(executePlainSQL("select code from Airport where city='$descity' AND country='$descountry'"));
			if (strcmp($_COOKIE['maxnumtrans'],"inf")==0){
				echo "<script>document.getElementById('maxnumtransinf').checked=true</script>";
				$flights = executePlainSQL("select * from allFlight 
											where firstid IN (select fid from Flight
							  			   					   where departap='$departap[0]')
					                        AND thirdid IN (select fid from Flight
					                        	             where arrivalap='$arrivalap[0]')
					                        AND dt1>='$flightdate'
					                        ORDER BY totalprice");
			} 
			else {	
				$flights = executePlainSQL("select * from allFlight where ((firstid IN (select fid from Flight
							  			   where departap='$departap[0]' AND arrivalap='$arrivalap[0]')
										    AND secondid IS NULL AND thirdid IS NULL) 
					                        OR (firstid IN (select fid from Flight
					                        				where departap='$departap[0]')
					                        AND secondid IN (select fid from Flight
					                        	             where arrivalap='$arrivalap[0]')
					                        AND thirdid IS NULL))
											AND dt1>='$flightdate'
					                        ORDER BY totalprice");
			}
			//print_r($flightdate);
			$locations = Array ($departap[0], $depcity, $depcountry, $arrivalap[0], $descity, $descountry);
			printFlights($flights, $locations);
		}		
	}
}
?>
</body>