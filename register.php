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
include 'pw.php';


$success = True; 
// Connect Oracle...
if ($db_conn) {
	
		if (array_key_exists('insertsubmit', $_POST)) {
			
			// check if email is taken
			$q = "select * from Customer where email = '".$_POST['email']."'";
			$numrows = countRows($db_conn, $q);
			
			if($numrows == 1) {
				$success = 0;
				?>
				<script type="text/javascript"> 
					alert("Error: Email address already registered. Please try a different email address"); 
				</script>
				<?php
			}
			else {
				$success = 1;
				
				//Getting the values from user and insert data into the table
				$tuple = array (
					":bind1" => $_POST['cid'],
					":bind2" => $_POST['email'],
					":bind3" => generate_hash($_POST['password']),
					":bind4" => $_POST['cname'],
					":bind5" => $_POST['passport_country'],
					":bind6" => $_POST['passport_num'],
					":bind7" => $_POST['phone#'],
					":bind8" => $_POST['address'],
					":bind9" => 0				
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
								:bind8,
								:bind9
								)", $alltuples);
								
				OCICommit($db_conn);
				
			}
		}		

	if ( $_POST && $success) {
				?>
				<script type="text/javascript"> 
					alert("Successfully registered. Click OK to be redirected to the login page.");
					location = "login.php";
				</script>
				<?php
	} 

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

?>

