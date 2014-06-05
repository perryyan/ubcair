<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">

<!-- Drop down menu for selecting specific table -->
<p>Select working data table</p>
<form method="POST" action="admin.php">
	<select name="tabchoice">
		<option value = ""></option>
		<option value = "CUSTOMER">Customers</option>
		<option value = "AIRPORT">Airports</option>
		<option value = "PLANE_IN">Airplanes</option>
		<option value = "FLIGHT">Flights</option>
		<option value = "MAKE_RES">Reservations</option>
		<option value = "RES_INCLUDES">Reserv-Flights*</option>
		<option value = "HAS_B">Baggages</option>
		<option value = "LAST_LOCATION">Baggage Locations</option>
		<option value = "DETER_PAY">Reserv-Payments*</option>
		<option value = "PAYMENT">Payments</option>		
	</select>
	<input type="submit" value="Submit" name="tabselect"></p>
</form>
<p>*These maybe hidden from staff view?</p>

<?php

// These stuff are needed (for now) to connect Oracle, will figure out how to import from
// main php file
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_b4s8", "a16894123", "ug");

// ExecutePlainSQL, executeBoundSQL are from a sample file in the CS304 Tutorial page, 
// http://www.ugrad.cs.ubc.ca/~cs304/2014S1/index.html,
// Author: Jiemin Zhang,
// Modified by Simona Radu
function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn); // For OCIParse errors pass the       
		// connection handle
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;

}

function executeBoundSQL($cmdstr, $list) {
	/* Sometimes a same statement will be excuted for severl times, only
	 the value of variables need to be changed.
	 In this case you don't need to create the statement several times; 
	 using bind variables can make the statement be shared and just 
	 parsed once. This is also very useful in protecting against SQL injection. See example code below for       how this functions is used */

	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	foreach ($list as $tuple) {
		foreach ($tuple as $bind => $val) {
			//echo $val;
			//echo "<br>".$bind."<br>";
			OCIBindByName($statement, $bind, $val);
			unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement); // For OCIExecute errors pass the statementhandle
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
	}

}

// Same as above, modified for generic table printing
function printResult($name, $cols, $data) { //prints results from a select statement
	echo "<br>Got data from table " . $name . "<br>";
	echo "<div class="."pure-table pure-table-bordered pure-table-striped"."><table>";
	// print the top row (attribute labels)
	$label = "<tr>";
	while ($row = OCI_Fetch_Array($cols, OCI_BOTH)) {
		$label = $label . "<th>" . $row[0] . "</th>";		
	}
	echo $label . "</tr>";
	// print the data rows (tuples)
	while ($tuple = OCI_Fetch_Array($data, OCI_BOTH)) {	
		$output = "<tr>";
				for ($it = 0; $it < count($tuple); $it = $it + 1) {
					$output = $output . "<td>" . $tuple[$it] . "</td>";
				}
				echo $output . "</tr>";
	}		
	echo "</table></div>";
}

// Only starts retrieving above forms when connection to Oracle is established
if ($db_conn) {
	// Get the drop down list selection
 	if (array_key_exists('tabselect', $_POST)) {
		$table = $_POST['tabchoice'];
		$cols = executePlainSQL("select column_name from user_tab_columns where table_name = '$table'");
		$data = executePlainSQL("select * from " . $table);
		printResult($table, $cols, $data);
	}	
	
	// This code is also from the tutorial page, not sure what it's for
	/*
	if ($_POST && $success) {
					//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
					header("location: admin.php");
				}
		OCILogoff($db_conn);*/
	
}
?>