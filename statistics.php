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
            <li><a href="index.php">Home</a></li>
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

<div class = "content-customer-area">
	Here are some statistics about the web site.<br><hr>
	
	<?php
	include 'oci_functions.php';
	if($db_conn) {
		
		echo "For airline companies that own at least 1 plane(s), show the number of times their planes have been used, and the average cost of their flights.<br>
		This is an example of nested aggregation.<br>";
		$q = "select p2.airline, p2.pid, count(f.fid) AS f_num, AVG(f.cost)
				from flight f, plane p2
				where f.pid = p2.pid AND p2.airline IN (
									 select p.airline
				                     from Plane p
				                     group by p.airline
				                     having COUNT(p.pid)>1)
				group by p2.airline, p2.pid
				order by p2.airline";
		
		highlight_string($q);
			
		$stmt = oci_parse($db_conn,$q);
		$r = oci_execute($stmt, OCI_DEFAULT);
		echo "<table class='pure-table pure-table-bordered'>
			<tr>
				<thead>
				<td>airline</td>
				<td>pid</td>
				<td>f_num</td>
				<td>AVG(f.cost)</td>
				</thead>
			</tr>";
		
		while($row = oci_fetch_array($stmt, OCI_BOTH)) {
			echo "<tr>".
					"<td>".$row[0]."</td>".
				  	"<td>".$row[1]."</td>".
				  	"<td>".$row[2]."</td>".
				  	"<td>".$row[3]."</td>".
				  "</tr>";
		}
		echo "</table>";
		echo "<hr>";
		
		?>
		<form method="POST" action="statistics.php"><table>
			<input type="number" placeholder="FID1" name="fid1" id="fid1"></input>
			<input type="number" placeholder="FID2" name="fid2" id="fid2"></input>
			<input type="submit"></input>
		</form>
		<?php
		echo "<p>Enter two fids and find the reservation IDs that use include both fids</p>";
		
		// Do the queries 				
		$q = "select r.resid
				from res_includes r
				where r.fid = '".$_POST['fid1']."' AND r.resid = (
									 select resid
                                     from res_includes r2
                                     where r2.fid = '".$_POST['fid2']."')";
		
		highlight_string($q);
		
		$stmt = oci_parse($db_conn, $q);
		$r = oci_execute($stmt, OCI_DEFAULT);
		
		echo "<table class='pure-table pure-table-bordered'>
			<tr>
				<thead>
				<td>Resid</td>
				</thead>
			</tr>";
		
		while($row = oci_fetch_array($stmt, OCI_BOTH)) {
			echo "<tr>".
					"<td>".$row[0]."</td>".
				  "</tr>";
				  
		}
		echo "</table>";
		echo "<hr>";
		
		echo "<p>Find the airline with the lowest average price:</p><br>";
		// This finds the airline with the lowest average price
		$q = "select p2.airline, count(f2.fid) AS flight_num, avg(f2.cost) AS avgCost
				from plane p2, flight f2
				where p2.pid = f2.pid
				group by p2.airline
				having avg(f2.cost) <= ALL (
							select avg(f.cost)
							from Plane p, flight f
							where p.pid = f.pid
							group by p.airline)";
		
		highlight_string($q);
		$stmt = oci_parse($db_conn, $q);		
		$r = oci_execute($stmt, OCI_DEFAULT);
		
		echo "<table class='pure-table pure-table-bordered'>
			<tr>
				<thead>
				<td>Airline</td>
				<td>flight_num</td>
				<td>Average cost</td>
				</thead>
			</tr>";
		
		while($row = oci_fetch_array($stmt, OCI_BOTH)) {
			echo "<tr>".
					"<td>".$row[0]."</td>".
					"<td>".$row[1]."</td>".
					"<td>".$row[2]."</td>".
				  "</tr>";
		}
		echo "</table>";
		echo "<hr>";
		
		OCILogoff($db_conn);
	}
	?>
</div>

</body>