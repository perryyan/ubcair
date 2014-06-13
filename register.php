<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="UBC Airline Booking Service">
	<title>UBCAir</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
	<link rel="stylesheet" href="css/mainpage.css"
</head>

<body>
<div class="header">
    <div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="">UBC Air</a>
        <ul>
            <li><a href="index.php">Home</a></li>
<?php

	if(!array_key_exists('loggedin', $_COOKIE) ) {
		echo "<li><a href='login.php'>Login</a></li>";
		echo "<li><a href='register.php'>Sign Up</a></li>";
	}
	else {
		echo "<li><a href='logut.php'>Logout</a></li>";
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
<form method ="POST" action="admin.php">
<button class="pure-button" name="Admin">Admin Page</button>
</form>	
<!--
<form method="POST" action="register.php">
<button class="pure-button" name="reset">Reset Database</button>
</form> -->

<!--refresh page when submit-->
<div class='content-customer-area'>
	<form class="pure-form pure-form-aligned" form method="POST" action="register.php">
	    <fieldset>
	            <div class="pure-control-group">
	                <label for="cname">Your Name</label>
	                <input id="cname" name="cname" type="text" maxlength="20" required>
	            </div>
	
	            <div class="pure-control-group">
	                <label for="email">Email</label>
	                <input id="email" name="email" type="email" maxlength="30" required>
	            </div>
	
	            <div class="pure-control-group">
	                <label for="password">Password</label>
	                <input id="password" name="password" type="password" maxlength="16" required>
	            </div>
	
	            <div class="pure-control-group">
	                <label for="passport_country">Passport Country</label>
	                <input id="passport_country" name="passport_country" type="text" maxlength="3" required>
	            </div>
	            
	            <div class="pure-control-group">
	                <label for="passport_num">Passport Number</label>
	                <input id="passport_num" name="passport_num" type="text"  maxlength="7"required>
	            </div>
	            
	            <div class="pure-control-group">
	                <label for="phone#">Phone Number</label>
	                <input id="phone#" name="phone#" type="text" maxlength="20" required>
	            </div>       
	               
	            <div class="pure-control-group">
	                <label for="address">Address</label>
	                <input id="address" name="address" type="text" maxlength="150" required>
	            </div>    
	        <button type="submit" class="pure-button pure-button-primary" name="insertsubmit">Submit</button>
	    </fieldset>
	</form>
</div>

</body>
</html>

<?php

include('oci_functions.php');

$success = True; 

function printResult($result) { //prints results from a select statement
	echo "<div class='content-customer-area'>";
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
			echo "<tr><td>" . $row[0] . "</td><td>" 
						. $row[1] . "</td><td>"
						. $row[2] . "</td><td>"
						. $row[3] . "</td><td>"
						. $row[4] . "</td><td>"
						. $row[5] . "</td><td>"
						. $row[6] . "</td><td>"
						. $row[7] . "</td></tr>"; //or just use "echo $row[0]" 
	
	}
	echo "</table></div></div>";
}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('reset', $_POST)) {
		// Drop old table...
		echo "<br> dropping table <br>";
		executePlainSQL("drop table Customer");
		executePlainSQL("drop table UserInfo");
		executePlainSQL("drop sequence cid_sequence");
		/*// Create new table...
		echo "<br> creating new table <br>";
		executePlainSQL("create table tab1 (nid number, name varchar2(30), primary key (nid))");
		OCICommit($db_conn);
		*/
		
		// Create user account table (Customers)
		executePlainSQL(
			"create table Customer(
			cid number PRIMARY KEY,
			email char(30) UNIQUE,
			password char(16),
			cname char(20),
			passport_country char(3),
			passport_num int,
			phone# char(20),
			address char(150))"
			);
			
		// Create sequence
		executePlainSQL("create sequence cid_sequence 
						start with 0 
						increment by 1 
						minvalue 0
						maxvalue 100000");

						
		OCICommit($db_conn);


	} else
		if (array_key_exists('insertsubmit', $_POST)) {
			
			// check if email is taken
			$q = "select count(*) from Customer where email = '".$_POST['email']."'";
			$numrows = countRows($db_conn, $q);
			if($numrows == 1) {
				echo "Error: Email address already registered. Please try a different email address";
				$success = 0;
			}
			
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
							:bind8
							)", $alltuples);
							
			OCICommit($db_conn);

		}		

	if ($_POST && $success) {
		header("location: register.php");
	} 

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

?>

