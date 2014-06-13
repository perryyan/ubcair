
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

<?php

session_start();

include('oci_functions.php');

// connect to database
if($db_conn) {
	
	if(!empty($_POST['email']) && !empty($_POST['password']) && !(array_key_exists('login', $_COOKIE)) ) {
	    $email = $_POST['email'];
		$password = $_POST['password'];
	
	    $loginquery = "SELECT * FROM Customer WHERE email = '".$email."' AND password = '".$password."'";

		$numrows = countRows($db_conn, $loginquery);


		if($numrows == 1) {

			$stmt = oci_parse($db_conn, $loginquery);
			$r = oci_execute($stmt, OCI_DEFAULT);
						
			$row = oci_fetch_array($stmt, OCI_BOTH);
			
			
			setcookie('loggedin',1);
			setcookie('cid', $row['CID']);
			setcookie('cname', $row['CNAME']);
			
			// Go to customer only page
			header('Location: support.php');
		}
	
		else {
			echo "<p>Invalid login or password. Click <a href='login.php'>here</a> to go back.<p>";
		}
	}
	
	else {
	?>
		<!-- Login Form -->
    <div class='content-customer-area'>
	    <form class="pure-form" form method="post" action="login.php" name="loginform" id="loginform">
	    <fieldset>
	        <legend>Login to UBC Air</legend>
	        <input type="email" placeholder="Email" name="email" id="email" required>
	        <input type="password" placeholder="Password" name="password" id="password" required>
	        <button type="submit" class="pure-button pure-button-primary" name="login" id="login">Sign in</button>
	    	<label for="remember">
          		<input id="remember" name="remember" type="checkbox"> Remember me
       		</label>
	    </fieldset>
	    </form>
		<a href="register.php">Don't have an account? Click here to register</a><br>
		<a href="forgot.php">Forgot password?</a>
	</div>
	</body>
<?php
	}

} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

?>

