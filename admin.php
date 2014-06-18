<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">

<!-- Drop down menu for selecting specific table -->
<p>Select working data table</p>
<form method="POST" action="admin.php">
	<select id="tabchoice" name="tabchoice" onchange="this.form.submit()">
		<option selected value = "default">(Choose a data table)</option>
		<option value = "CUSTOMER">Customers</option>
		<option value = "AIRPORT">Airports</option>
		<option value = "PLANE">Airplanes</option>
		<option value = "FLIGHT">Flights</option>
		<option value = "MAKE_RES">Reservations</option>
		<option value = "RES_INCLUDES">Reserv-Flights*</option>
		<option value = "HAS_B">Baggages</option>
		<option value = "LAST_LOCATION">Baggage Locations</option>
		<option value = "DETER_PAY">Reserv-Payments*</option>
		<option value = "PAYMENT">Payments</option>		
	</select>
	<input type="number" max="5" value ="0" name="test">
	<noscript><input type="submit" value="Submit"></noscript></p>
</form>

<script>
<!-- Check inputs in insert form -->
	function validateInsert() {
		var elements = document.forms["insertform"].elements;
	    for (e in elements) {
	    	if (elements[e].name == "tablename") break;
	    	var validity = isValid(elements[e].name, elements[e].value);
	    	if (!validity) return false;
	    }
   		 return true;	
	}
	
<!-- Check inputs in update form-->
	function validateUpdate() {
		var elements = document.forms["updateform"].elements;
		var validity = isValid(elements["field2change"].value, elements["newvalue"].value);
		var validity2 = isValid(elements["updatesearchby"].value, elements["searchedvalue"].value);	
		if (!validity || !validity2) return false;
		else return true;
	}

<!-- Check input in delete form -->
	function validateDelete() {
		var elements = document.forms["deleteform"].elements;
		return isValid(elements["deletesearchby"].value, elements["searchedvalue"].value);	
	}

<!-- Helper, actually checks given input vs. attribute name, using regular expressions -->
	function isValid(name, value) {
		var pattern;
		switch (name) {
		case "RESID":
		case "RESORDER":
		case "BID":
		case "STATUS":
		case "CID":
		case "PCLASS":
		case "TICKET_NUM":
		case "FID":
		case "PAYID":
	    case "PHONE":
	    case "COST":
	    case "TOTAL_COST":
		case "PASSPORT_NUM":
		case "IS_ADMIN":
		case "CID": pattern = /(^\d\d*\d$)|(^\d$)/; break;
		case "CREDITCARD": pattern = /^\d\d{14}\d$/; break;
		case "WEIGHT_KG": pattern = /(^(\d*)\.{0,1}(\d*)$)|(^\d$)/; break;
		case "EMAIL": pattern = /^\w\w*(\.\w+)*@\w+\.\w+$/; break;
		case "DEPARTAP":
		case "ARRIVALAP":
		case "CODE": pattern = /^[a-zA-Z][a-zA-Z][a-zA-Z]$/; break;
		case "COUNTRY":
		case "PASSPORT_COUNTRY": pattern = /^[a-zA-Z][a-zA-Z]$/; break;
		case "DEPARTTIME":
		case "ARRIVALTIME": pattern = /.+/; break;			
		default: pattern = /^\w\w*\w$/;
		}
		if (!pattern.test(value)) {
			alert("Invalid input, " + name + " has regex " + pattern);
			return false;
		}
		else return true; 
	}
</script>
<?php

// These stuff are needed (for now) to connect Oracle, will figure out how to import from
// main php file
$success = True; //keep track of errors so it redirects the page only if there are no errors
//$db_conn = OCILogon("ora_b4s8", "a16894123", "ug");

include "oci_functions.php"; 

// Same source as above, modified for generic table printing
// Prints everything after table is selected
function printResults($name, $cols, $data) {
	$attributes = array();
	while ($row = OCI_Fetch_Array($cols, OCI_NUM)) {
		$attributes[] = $row[0];
	}
	echo "<br>INSERT NEW<br>(enter value for each attribute)";
	printInsertFields($name, $attributes);
	echo "<br>UPDATE<br>(search for the row to change, then choose attribute to change and enter new value)<br>";
	printUpdateFields($name, $attributes);
	echo "<br>DELETE<br>(search for the row to delete)<br>";
	printDeleteFields($name, $attributes);
	echo "<br>DATA FROM TABLE " . $name . "<br>";
	printTable($attributes, $data);
}

// Prints the table attributes and data		
function printTable($attributes, $data) { 
	echo "<div class="."pure-table pure-table-bordered pure-table-striped"."><table>";
	// print the top row (attribute labels)
	$label = "<tr>";
	foreach ($attributes as $value) {
		$label = $label . "<th>" . $value . "</th>";		
	}
	echo $label . "</tr>";
	// print the data rows (tuples)
	while ($tuple = OCI_Fetch_Array($data, OCI_NUM)) {
		$output = "<tr>";
		foreach($tuple as $value) {
			$output = $output . "<td>" . $value . "</td>";
		}
		echo $output . "</tr>";
	}		
	echo "</table></div>";
}

// Prints the form to insert new tuple into chosen table
function printInsertFields($table, $attributes) {
	$form = "<form method='POST' name='insertform' action='admin.php' onsubmit='return validateInsert()'><table>";
	for ($it=0; $it < count($attributes); $it++) {
		if (strcmp($attributes[$it],"DEPARTTIME") == 0 || strcmp($attributes[$it],"ARRIVALTIME") == 0) $type = "datetime-local";
		else $type = "text";
		$form = $form . "<tr><td>" . $attributes[$it] . ":</td><td><input type='$type' name = '$attributes[$it]' required></td></tr>";		
	} 
	echo $form . "</table>
				  <p><input type='hidden' value='$table' name='tablename'></p>
				  <p><input type='submit' value='Insert' name='insertsubmit'></p>
			      </form>";
}

// Prints the form to update table data
function printUpdateFields($table, $attributes) {
	$form = "<form method='POST' name ='updateform' action='admin.php' onsubmit='return validateUpdate()'><table>" 
	. "<tr><td>Search row by: </td><td><select id='updatesearchby' name='updatesearchby'>"
	. "<option selected value='default'>(Select Column)</option>";
	for ($it=0; $it < count($attributes); $it++) {
		$form = $form . "<option value ='$attributes[$it]'>$attributes[$it]</option>";		
	} 
	$form = $form . "</select></td>"
		. "<td><input type='text' name='searchedvalue' placeholder='Value to search'></td>"
		. "</tr>"
		. "<tr><td>Attribute to update: </td><td><select id='field2change' name='field2change'>"
		. "<option selected value='default'>(Select Column)</option>";
	for ($it=0; $it < count($attributes); $it++) {
		$form = $form . "<option value ='$attributes[$it]'>$attributes[$it]</option>";		
	}
	$form = $form . "</select></td>"
		. "<td><input type='text' name='newvalue' placeholder='Enter new value'></td>"
		. "</tr></table>"
		. "<p><input type='hidden' value='$table' name='tablename'></p>"
		. "<p><input type='submit' value='Update' name='updatesubmit'></p></form>";	
	echo $form;
}
// Prints the form to delete table data
function printDeleteFields($table, $attributes) {
	$form = "<form method='POST' name='deleteform' action='admin.php' onsubmit='return validateDelete()'><table>" 
	. "<tr><td>Search row by: </td><td><select id='deletesearchby' name='deletesearchby'>"
	. "<option selected value='default'>(Select Column)</option>";
	for ($it=0; $it < count($attributes); $it++) {
		$form = $form . "<option value ='$attributes[$it]'>$attributes[$it]</option>";		
	} 
	$form = $form . "</select></td>"
		. "<td><input type='text' name='searchedvalue' placeholder='Value to search'></td>"
		. "</tr></table>"
		. "<p><input type='hidden' value='$table' name='tablename'></p>"
		. "<p><input type='submit' value='Delete' name='deletesubmit'></p></form>";	
	echo $form;
}

// Only starts retrieving above forms when connection to Oracle is established
if ($db_conn) {
	// Get the drop down table selection and call function to deal with selected table
 	if (array_key_exists('tabchoice', $_POST)) {
		// save selection for future load
		setcookie("tabchoice",$_POST['tabchoice']);
	}
	// Handle tuple insert form submission
	if (array_key_exists('insertsubmit', $_POST)) {
		reset($_POST);		
		list($key, $value) = each($_POST);
		$tuple = "'$value'";
		while (list($key, $value) = each($_POST)) {
			if (strcmp($key,"DEPARTTIME") == 0 || strcmp($key,"ARRIVALTIME") == 0) {
				$value = parseDate($value,3);
			}
			if (strcmp($key,"insertsubmit") == 0 || strcmp($key,"tablename") == 0) break;
			$tuple = "$tuple,'$value'";
		}
		$tablename = $_POST['tablename'];
		executePlainSQL("insert into $tablename values ($tuple)");
		OCICommit($db_conn);
	}
	// Handle tuple update form submission
	if (array_key_exists('updatesubmit', $_POST)) {
		$tablename = $_POST['tablename'];
		$field2change = $_POST['field2change']; 
		$newvalue = $_POST['newvalue'];
		$updatesearchby = $_POST['updatesearchby'];
		$searchedvalue = $_POST['searchedvalue'];
		executePlainSQL("update $tablename" 
				. " set $field2change ='$newvalue'" 
				. " where $updatesearchby = '$searchedvalue'");
		OCICommit($db_conn);
		
		// save selection for next load
		setcookie("updatesearchby",$updatesearchby);
	}
	// Handle delete from submission
	if (array_key_exists('deletesubmit', $_POST)) {
		$tablename = $_POST['tablename']; 
		$deletesearchby = $_POST['deletesearchby'];
		$searchedvalue = $_POST['searchedvalue'];
		executePlainSQL("delete from $tablename"  
				. " where $deletesearchby = '$searchedvalue'");
		OCICommit($db_conn);
		
		// save selection for next load
		setcookie("deletesearchby",$deletesearchby);
	}
	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: admin.php");
	// default will check if there is any table already selected and output that	
	} else if ((strcmp($_COOKIE['tabchoice'], "") !== 0) && (strcmp($_COOKIE['tabchoice'], "default") !== 0)) {
		$tabchoice = $_COOKIE['tabchoice'];		
		$cols = executePlainSQL("select column_name from user_tab_columns where table_name = '$tabchoice'");
		$data = executePlainSQL("select * from " . $tabchoice);
		printResults($tabchoice, $cols, $data);
		
		// retrieve past delete/update selection from cookie for this load
		$updatesearchby = $_COOKIE['updatesearchby'];
		$deletesearchby = $_COOKIE['deletesearchby'];
		// delete them as only needed for one load
		setcookie("updatesearchby", "", time()-3600);
		setcookie("deletesearchby", "", time()-3600);
		// set the drop down lists according to the cookie (last delete/update selections)
		echo "<script>document.getElementById('tabchoice').value='$tabchoice'</script>";
		if (strcmp($_COOKIE['updatesearchby'], "") !== 0) {
		echo "<script>document.getElementById('updatesearchby').value='$updatesearchby'</script>";
		}
		if (strcmp($_COOKIE['deletesearchby'], "") !== 0) {
		echo "<script>document.getElementById('deletesearchby').value='$deletesearchby'</script>";
		}
	}
	OCILogoff($db_conn);
}
?>