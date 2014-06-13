<?php 

// Changing the format of Oracle's timestamp data for more friendly look,
// mode 1 for timestamp, mode 2 for intervals (result of algebraic operations on timestamps)
function parseDate($value, $mode) {
	if ($mode == 1) return substr($value, 0, 17);
	if ($mode == 2) return substr($value, 10, 9);	
}

// Coordinate printing of detailed information regarding each flight on the search result when clicked
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

// Actually printing detailed information regarding each flight in search result 
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

// Another helper for printDetails, printing wait time between transfer
function printLayOver($firstid, $secondid) {
	$layover = oci_fetch_row(executePlainSQL("select F2.departtime-F1.arrivaltime from Flight F1, Flight F2
									where F1.fid='$firstid' AND F2.fid='$secondid'"));
	$layovertime = parseDate($layover[0],2);
	echo "<br>Lay over for $layovertime";	
}

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

// This function counts the number of rows (tuples) given a raw query (before parse and execution)
function countRows($db_conn, $query) {
		
	$numrows = 0;

	$stmt = oci_parse($db_conn, $query);
	$r = oci_execute($stmt, OCI_DEFAULT);
	
	if($r) {
		oci_fetch_all($stmt, $result);
		$numrows = oci_num_rows($stmt);
		oci_free_statement($stmt);
		return $numrows;
	}
}

?>