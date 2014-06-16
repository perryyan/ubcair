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
		header('location: login.php');
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
        </ul>
    </div>
</div>

	<div class="banner">
    <h1 class="banner-head">
        Welcome to UBC Air
    </h1>
</div>
<?php
	include "oci_functions.php"; 
	
	if($db_conn) {

		$cid = $_COOKIE['cid'];
		$cname = "";
		$email = "";
		$addr = "";
		$phone = "";
		$passport_country = "";
		$passport_num = 0;
		
		// Handle POST
		if (array_key_exists('updateprofile', $_POST)) {
			?>
				<script type="text/javascript"> 
					alert("Updated profile successfully.");
					location: "profile.php";
				</script>
			<?php
								
			$update_profile_q = "UPDATE Customer SET
								email='".$_POST['email']."',
								cname='".$_POST['cname']."',
								passport_country='".$_POST['passport_country']."',
								passport_num=".$_POST['passport_num'].",
								phone='".$_POST['phone']."',
								address='".$_POST['address']."'
								where cid=".$_COOKIE['cid'];	
								
			executePlainSQL($update_profile_q);
			
			OCICommit($db_conn);		
		}
	
		if (array_key_exists('changepw', $_POST)) {
			// check old pw and compare it to the DB tuple
			$q = "select password from Customer where cid = '".$cid."'";
			$result = executePlainSQL($q);
			$row = OCI_Fetch_Array($result, OCI_BOTH);
			$password = $row['PASSWORD'];			
			if(strcmp($password, $_POST['curr_pw']) != 0) {
				// incorrect, throw a JS popup
				?>
				<script type="text/javascript"> 
					alert("Error: Old password does not match.");
					location: "profile.php";
				</script>
			<?php
		}
		
		else {
			if(strcmp($_POST['new_pw'], $_POST['new_pw_2']) != 0 ) {
			// new passwords do not match, throw a JS popup
			?>
				<script type="text/javascript"> 
					alert("Error: New passwords do not match.");
					location: "profile.php";				
				</script>
			<?php
			}
			
			// Correct password and both new passwords match
			else {
				$update_pw_q = "UPDATE Customer SET password = '".$_POST['new_pw']."' WHERE cid = ".$cid;
				executePlainSQL($update_pw_q);
				OCICommit($db_conn);
				?>
				<script type="text/javascript"> 
					alert("Password successfully updated!");
					location: "profile.php";
				</script>
				<?php
	
			}
		}
	}	
		// Fetch the customer profile based on the cid from cookies	
		$q = "select * from Customer where cid = '".$cid."'";
		$result = executePlainSQL($q);
		$row = OCI_Fetch_Array($result, OCI_BOTH);
		
		$cname = $row['CNAME'];
		$email = $row['EMAIL'];
		$addr = $row['ADDRESS'];
		$phone = $row['PHONE'];
		$passport_country = $row['PASSPORT_COUNTRY'];
		$passport_num = $row['PASSPORT_NUM'];	
		
	OCILogoff($db_conn);
}
		
		
?>
<div class="content-customer-area">

<p>Edit your profile:</p>
<form class="pure-form pure-form-aligned" method="POST" action="profile.php">
    <fieldset>
        <div class="pure-control-group">
            <label for="name">Name</label>
            <input id="name" type="text" name="cname" value="<?php echo $cname;?>">
        </div>

        <div class="pure-control-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="<?php echo $email;?>">
        </div>
        
        <div class="pure-control-group">
            <label for="address">Address</label>
            <input id="address" type="text" name="address" value="<?php echo $addr;?>">
        </div>
            
        <div class="pure-control-group">
            <label for="phone">Phone Number</label>
            <input id="phone" type="text" name="phone" value="<?php echo $phone;?>">
        </div>    
            
        <div class="pure-control-group">
            <label for="passport_country">Passport Country</label>
            <input id="passport_country" type="text" name="passport_country" value="<?php echo $passport_country;?>">
        </div>         
            
        <div class="pure-control-group">
            <label for="passport_num">Passport Number</label>
            <input id="passport_num" type="number" name="passport_num" value="<?php echo $passport_num;?>">
        </div>     
          
        <div class="pure-controls">
            <button type="submit" name="updateprofile" class="pure-button pure-button-primary">Save changes</button>
        </div>
    </fieldset>                   
</form>
<p>Change password:</p>                         
<form class="pure-form pure-form-aligned" method="POST" action="profile.php">
    <fieldset>   
        <div class="pure-control-group">
            <label for="curr_pw">Current Password</label>
            <input id="curr_pw" name="curr_pw" type="password">
        </div>   
            
        <div class="pure-control-group">
            <label for="new_pw">New Password</label>
            <input id="new_pw" name="new_pw" type="password">
        </div>         
            
        <div class="pure-control-group">
            <label for="new_pw_2">New Password (confirm)</label>
            <input id="new_pw_2" name="new_pw_2" type="password">
        </div>     
        <div class="pure-controls">
            <button type="submit" name="changepw" class="pure-button pure-button-primary">Save changes</button>
        </div>
    </fieldset>                   
</form>
</div>
</body>