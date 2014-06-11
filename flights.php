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
<tr><input type="submit" value="Submit" name="searchsubmit"></tr>
</table>
</form>

<?php
	
// These stuff are needed (for now) to connect Oracle, will figure out how to import from
// main php file
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_b4s8", "a16894123", "ug");
	
include "oci_functions.php";

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

// Prints the table attributes and data		
function printFlights($flights) {
	echo "<p><br>Search Results: <br></p>"; 
	echo "<div class="."pure-table pure-table-bordered pure-table-striped"."><table border='1'>";
	// print the top row (attribute labels)
	echo "<tr><th>ID</th><th>Departure Airport</th><th>City</th><th>Country</th>"
			."<th>Arrival Airport</th><th>City</th><th>Country</th><th>Departure Time</th>"
	    	."<th>Arrival Time</th><th>COST</th></tr>";
	// print the data rows (tuples)
	while ($tuple = OCI_Fetch_Array($flights, OCI_ASSOC)) {
		$output = "<tr>";
		foreach($tuple as $value) {
			$output = $output . "<td>" . $value . "</td>";
		}
		echo $output . "</tr>";
	}		
	echo "</table></div>";
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
			executePlainSQL("drop View FlightSearchDisplay");
			executePlainSQL("create View FlightSearchDisplay(fid,depapname,depcity,depcountry, desapname,descity,descountry,deptime,destime,cost) AS"
						  ." select F.fid, A1.apname, A1.city, A1.country, A2.apname, A2.city, A2.country, F.departtime, F.arrivaltime, F.cost"
						  ." from Flight F, Airport A1, Airport A2"
						  ." where F.departap = A1.code AND F.arrivalap = A2.code"
						  ." AND A1.city='$depcity' AND A1.country='$depcountry'"
						  ." AND A2.city='$descity' AND A2.country='$descountry'");
			$flights = executePlainSQL("select * from FlightSearchDisplay order by cost");
			printFlights($flights);
		}		
	}
}
?>