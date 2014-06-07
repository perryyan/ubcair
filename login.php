<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="UBC Airline Booking Service">
	<title>UBCAir</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
	<link rel="stylesheet" href="css/mainpage.css"
</head>

<body>
	<div class="banner">
    <h1 class="banner-head">
        Welcome to UBC Air
    </h1>
</div>
</body>

<?php

include('oci_functions.php');

	// connect to database
$db_conn = OCILogon("ora_c2e8", "a42375105", "ug");
if($db_conn) {
		
	if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])) {
    // let the user access the main page
    
    echo "<p>You are logged in as";
    echo $_SESSION['Username'];
    echo "and your email address is";
    echo $_SESSION['EmailAddress'];
    
    
	}
	
	else if(!empty($_POST['email']) && !empty($_POST['password'])) {
	    // let the user login
		
	    $email = $_POST['email'];
		$password = $_POST['password'];
	    $loginquery = "SELECT * FROM Customer WHERE email = '".$email."' AND password = '".$password."'";
	
		$numrows = countRows($db_conn, $loginquery);

		if($numrows == 1) {
			$stmt = oci_parse($db_conn, $loginquery);
			$r = oci_execute($stmt, OCI_DEFAULT);
						
			while( $row = oci_fetch_array($stmt, OCI_BOTH) ) {
				echo "Welcome, ".$row['CNAME']
				."! You are customer id #".$row['CID']
				.". Your email address is ".$row['EMAIL'];
			}
				
			oci_free_statement($stmt);
			oci_close($db_conn);
		}
		else {
			echo "<p>Invalid login or password<p>";
		}	
	}
	
	else {
    // display the login form
    ?>
    <form class="pure-form" form method="post" action="login.php" name="loginform" id="loginform">
    <fieldset>
        <legend>Login to UBC Air</legend>
        <input type="email" placeholder="Email" name="email" id="email" required>
        <input type="password" placeholder="Password" name="password" id="password" required>
        <button type="submit" class="pure-button pure-button-primary" name="login" id="login">Sign in</button>
    </fieldset>
    </form>
	<a href="register.php">Register</a><br>
	<a href="forgot.php">Forgot password?</a>
	
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