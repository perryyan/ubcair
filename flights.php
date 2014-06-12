<p>Search for your perfect flight<br></p>
<p><a href="http://www.textfixer.com/resources/dropdowns/country-list-iso-codes.txt" target="_blank">Country Codes</a><br></p>
<!--Drop down list frames, contents will be dynamically created-->
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
<tr>
<td>Number of transfers: </td>
<td><input type="radio" checked name="maxnumtrans" value="1">0-1</td>
<td><input type="radio" name="maxnumtrans" id="maxnumtransinf" value="inf">2+</td>
</tr>
</table>
<input type="submit" value="Submit" name="searchsubmit">
</form>

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
$db_conn = OCILogon("ora_b4s8", "a16894123", "ug");
	
//include "flightdetails.php";
include "oci_functions.php";

function parseDate($value, $mode) {
	if ($mode == 1) return substr($value, 0, 17);
	if ($mode == 2) return substr($value, 10, 9);	
}

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

function printDetailsHelper($flightid) {
	$flight = oci_fetch_array(executePlainSQL("select departap,arrivalap,departtime,arrivaltime,cost,arrivaltime-departtime as ftime from Flight"
										    ." where fid='$flightid'"),OCI_ASSOC);
	$departapcode = $flight['DEPARTAP'];
	$departap = oci_fetch_array(executePlainSQL("select * from Airport"
											  ." where code='$departapcode'"));
	$departapname = $departap['APNAME'];
	$departapcity = $departap['CITY'];
	$departapcountry = $departap['COUNTRY'];
	$departtime = parseDate($flight['DEPARTTIME'], 1);
	$arrivalapcode = $flight['ARRIVALAP'];
	$arrivalap = oci_fetch_array(executePlainSQL("select * from Airport"
											  	." where code='$arrivalapcode'"));
	$arrivalapname = $arrivalap['APNAME'];
	$arrivalapcity = $arrivalap['CITY'];
	$arrivalapcountry = $arrivalap['COUNTRY'];
	$arrivaltime = parseDate($flight['ARRIVALTIME'], 1);
	$fduration = parseDate($flight['FTIME'],2);
	echo "<br>Depart from $departapname ($departapcode at $departapcity, $departapcountry) on $departtime GMT"
		."<br>Flight Duration: $fduration"
    	."<br>Arrive at $arrivalapname ($arrivalapcode at $arrivalapcity, $arrivalapcountry) on $arrivaltime GMT";
}

function printLayOver($firstid, $secondid) {
	$layover = oci_fetch_row(executePlainSQL("select F2.departtime-F1.arrivaltime from Flight F1, Flight F2
									where F1.fid='$firstid' AND F2.fid='$secondid'"));
	$layovertime = parseDate($layover[0],2);
	echo "<br>Lay over for $layovertime";	
}

function printDetails($route, $it) {
	echo "<a href='#' class='toggler' detail-num='$it'>Details</a>"
    	."<a class='detail$it' style='display:none'>";
	if (array_key_exists('FIRSTID', $route)) {
		$firstid = $route['FIRSTID'];
		printDetailsHelper($firstid);
	}
	if (array_key_exists('SECONDID',$route)) {
		$secondid = $route['SECONDID'];
		printLayOver($firstid, $secondid);		
		printDetailsHelper($secondid);
	}	
	if (array_key_exists('THIRDID', $route)) {
		$thirdid = $route['THIRDID'];
		printLayOver($secondid, $thirdid);
		printDetailsHelper($route['THIRDID']);
	}
	echo "</a>";
}
// Prints the table attributes and data		
function printFlights($flights, $locations) {
	echo "<p><br>Search Results: <br></p>";
	echo "<form method='POST' action='reservation.php'>";
	echo "<div class="."pure-table pure-table-bordered pure-table-striped"."><table border='1'>";
	// print the top row (attribute labels)
	echo "<tr><th>Departure Airport</th><th>City</th><th>Country</th>"
			."<th>Arrival Airport</th><th>City</th><th>Country</th><th>Departure Time (GMT)</th>"
	    	."<th>Total Flight Time</th><th>COST (CAD)</th><th>Choose Flight</th></tr>";
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
		$flight_string = implode(" ",$flight);
		echo $printout . "<td>$departtime</td><td>$flighttime</td><td>$cost</td>";
		echo "<td><input type='radio' name='flightchoice' value='$flight_string' required></td></tr>";
		echo "<tr><td>";
		printDetails($flight, $it);
		echo "</td></tr>";
		$it++;
	}		
	echo "</table></div>";
	echo "<input type='submit' value='Book my flight'></form>";
}

if ($db_conn) {
	// Get the drop down table selection and call function to deal with selected table	
	//if (array_key_exists('searchsubmit', $_POST)) {
	//	setcookie('depcity',$_POST['depcity']);
	//	setcookie('descity',$_POST['descity']);
	//}
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
	
	if ($_POST && $success) {
		header("location: flights.php");
	}
	else {
		$depcity; $descity;
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
		// magic happens here
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
					                        ORDER BY totalprice");
			} 
			else {	
				$flights = executePlainSQL("select * from allFlight where (firstid IN (select fid from Flight
							  			   where departap='$departap[0]' AND arrivalap='$arrivalap[0]')
										    AND secondid IS NULL AND thirdid IS NULL) 
					                        OR (firstid IN (select fid from Flight
					                        				where departap='$departap[0]')
					                        AND secondid IN (select fid from Flight
					                        	             where arrivalap='$arrivalap[0]')
					                        AND thirdid IS NULL)
					                        ORDER BY totalprice");
			}
			$locations = Array ($departap[0], $depcity, $depcountry, $arrivalap[0], $descity, $descountry);
			printFlights($flights, $locations);
		}		
	}
}
?>