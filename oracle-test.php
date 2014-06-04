<!--Test Oracle file for UBC CPSC304 2011 Winter Term 2
  Created by Jiemin Zhang
  Modified by Simona Radu
  This file shows the very basics of how to execute PHP commands
  on Oracle.  
  specifically, it will drop a table, create a table, insert values
  update values, and then query for values
 
  IF YOU HAVE A TABLE CALLED "tab1" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the 
  OCILogon below to be your ORACLE username and password -->
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">

<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>
<form method="POST" action="oracle-test.php">
   
<p><input type="submit" value="Reset" name="reset"></p>
</form>

<p>Insert values into tab1 below:</p>
<form method="POST" action="oracle-test.php">
<!--refresh page when submit-->

<!--<p>cid<input type="text" name="cid" size="6"> -->
<br>email<input type="text" name="email" size="18">
<br>password<input type="text" name="password" size="18">
<br>cname<input type="text" name="cname" size="18">  
<br>passport_country<input type="text" name="passport_country" size="4">
<br>passport_number<input type="text" name="passport_num" size="18">
<br>phone#<input type="text" name="phone#" size="18">
<br>address<input type="text" name="address" size="18">
<!--define two variables to pass the value-->
      
<input type="submit" value="insert" name="insertsubmit"></p>
</form>
<!-- create a form to pass the values. See below for how to 
get the values--> 



<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_c2e8", "a42375105", "ug");

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

function printResult($result) { //prints results from a select statement
	echo "<br>Got data from table:<br>";
	echo "<div class="."pure-table pure-table-bordered pure-table-striped"."><table>";
	echo "<tr><th>cid</th>"
		 ."<th>email</th>"
		 ."<th>password</th>"
		 ."<th>cname</th>"
		 ."<th>passport_country</th>"
		 ."<th>passport_num</th>"
		 ."<th>phone#</th>"
		 ."<th>address</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["CID"] . "</td><td>" 
						. $row["EMAIL"] . "</td><td>"
						. $row["PASSWORD"] . "</td><td>"
						. $row["CNAME"] . "</td><td>"
						. $row["PASSPORT_COUNTRY"] . "</td><td>"
						. $row["PASSPORT_NUM"] . "</td><td>"
						. $row["PHONE#"] . "</td><td>"
						. $row["ADDRESS"] . "</td></tr>"; //or just use "echo $row[0]" 
	
	/*		echo "<tr><td>" . $row[0] . "</td><td>" 
						. $row[1] . "</td><td>"
						. $row[2] . "</td><td>"
						. $row[3] . "</td><td>"
						. $row[4] . "</td><td>"
						. $row[5] . "</td><td>"
						. $row[6] . "</td><td>"
						. $row[7] . "</td></tr>"; //or just use "echo $row[0]" 
	*/
	}
	echo "</table></div>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('reset', $_POST)) {
		// Drop old table...
		echo "<br> dropping table <br>";
		executePlainSQL("drop table Customer");
		executePlainSQL("drop sequence cid_sequence");
		/*// Create new table...
		echo "<br> creating new table <br>";
		executePlainSQL("create table tab1 (nid number, name varchar2(30), primary key (nid))");
		OCICommit($db_conn);
		*/
		
		// Create user account table (Customers)
		echo "<br> Creating customer table <br>";
		executePlainSQL(
			"create table Customer(
			cid number PRIMARY KEY,
			email char(30) UNIQUE,
			password char(16),
			cname char(20),
			passport_country char(3),
			passport_num int,
			phone# char(20),
			address char(150))");
			
			
		// Create sequence
		executePlainSQL("create sequence cid_sequence 
						start with 1 
						increment by 1 
						minvalue 1
						maxvalue 100000");

						
		OCICommit($db_conn);


	} else
		if (array_key_exists('insertsubmit', $_POST)) {
			//Getting the values from user and insert data into the table
			$tuple = array (
				":bind1" => $_POST['cid'],
				":bind2" => $_POST['email'],
				":bind3" => $_POST['password'],
				":bind4" => $_POST['cname'],
				":bind5" => $_POST['passport_country'],
				":bind6" => $_POST['passport_num'],
				":bind7" => $_POST['phone#'],
				":bind8" => $_POST['address']				
			);
			$alltuples = array (
				$tuple
			);
			
			executeBoundSQL("insert into Customer values (
							cid_sequence.nextval, 	
							:bind2, 
							:bind3, 
							:bind4, 
							:bind5, 
							:bind6, 
							:bind7, 
							:bind8)", $alltuples);
			OCICommit($db_conn);

		} else
			
			if (array_key_exists('updatesubmit', $_POST)) {
				// Update tuple using data from user
				$tuple = array (
					":bind1" => $_POST['oldName'],
					":bind2" => $_POST['newName']
				);
				$alltuples = array (
					$tuple
				);
				executeBoundSQL("update tab1 set name=:bind2 where name=:bind1", $alltuples);
				OCICommit($db_conn);
				

			} else
				if (array_key_exists('dostuff', $_POST)) {

				}
				

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: oracle-test.php");
	} else {
		// Select data...
		$result = executePlainSQL("select * from Customer");
		printResult($result);
	}

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

/* OCILogon() allows you to log onto the Oracle database
     The three arguments are the username, password, and database
     You will need to replace "username" and "password" for this to
     to work. 
     all strings that start with "$" are variables; they are created
     implicitly by appearing on the left hand side of an assignment 
     statement */

/* OCIParse() Prepares Oracle statement for execution
      The two arguments are the connection and SQL query. */
/* OCIExecute() executes a previously parsed statement
      The two arguments are the statement which is a valid OCI
      statement identifier, and the mode. 
      default mode is OCI_COMMIT_ON_SUCCESS. Statement is
      automatically committed after OCIExecute() call when using this
      mode.
      Here we use OCI_DEFAULT. Statement is not committed
      automatically when using this mode */

/* OCI_Fetch_Array() Returns the next row from the result data as an  
     associative or numeric array, or both.
     The two arguments are a valid OCI statement identifier, and an 
     optinal second parameter which can be any combination of the 
     following constants:

     OCI_BOTH - return an array with both associative and numeric 
     indices (the same as OCI_ASSOC + OCI_NUM). This is the default 
     behavior.  
     OCI_ASSOC - return an associative array (as OCI_Fetch_Assoc() 
     works).  
     OCI_NUM - return a numeric array, (as OCI_Fetch_Row() works).  
     OCI_RETURN_NULLS - create empty elements for the NULL fields.  
     OCI_RETURN_LOBS - return the value of a LOB of the descriptor.  
     Default mode is OCI_BOTH.  */
?>
