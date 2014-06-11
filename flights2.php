<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
<link rel="stylesheet" href="css/mainpage.css">
<title>UBC Air - Find flights</title>
</head>

<body>
	<div class="header">
    <div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="">UBC Air</a>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Sign Up</a></li> 
            <li><a href="flights.php">Find flights</a></li>   
        </ul>
    </div>
	<script>
	$(document).ready(function(){
    	$(".toggler").click(function(e){
        	e.preventDefault();
       		$('.detail'+$(this).attr('detail-num')).toggle();
    	});
	});
	</script>
	
<form class="pure-form" method ="POST" action="payment.php">
<fieldset>
    <table class="pure-table pure-table-bordered">
    <tr>
        <td>Departing from</td>
        <td>Departure time</td>
        <td>Arriving at</td>
        <td>Arrival time</td>
        <td>Duration</td>
        <td>Price</td>
        <td>Select this flight</td>
    </tr>
    <tr>
        <td>
            YVR
            <br>Vancouver International Airport
        </td>
        <td>8:00PM</td>
        <td>
            HKG
            <br>Hong Kong International Airport    
        </td>
        <td>12:00PM</td>
        <td>20 hours</td>
        <td>$1300</td>
        <td>
            <label for="flight1" class="pure-radio">
                <input id="flight1" type="radio" name="optionsRadios" value="option1">
            </label>
        </td>
    </tr>
    </table>
    <a href="#" class="toggler" detail-num="1">Details</a>
    
<a class="detail1" style="display:none">
    <br>
    <br>Depart from YVR (Vancouver International Airport) at 8:00 PM
    <br>Arrive at SEA (Seattle International Airport) at 9:45 PM
    <br>Layover for 1 hour
    <br>Depart from SEA (Seattle International Airport) at 10:45 PM
    <br>Arrive at HKG (Hong Kong International Airport) at 12:00 PM
        </a>

<table class="pure-table pure-table-bordered">
    <tr>
        <td>Departing from</td>
        <td>Departure time</td>
        <td>Arriving at</td>
        <td>Arrival time</td>
        <td>Duration</td>
        <td>Price</td>
        <td>Select this flight</td>
    </tr>
    <tr>
        <td>
            YVR
            <br>Vancouver International Airport    
        </td>
        <td>5:00PM</td>
        <td>
            HKG
            <br>Hong Kong International Airport    
        </td>
        <td>4:24PM</td>
        <td>14 hours</td>
        <td>$1450</td>
        <td>
            <label for="flight2" class="pure-radio">
                <input id="flight2" type="radio"        name="optionsRadios" value="option2"></label>
        </td>
    </tr>
    </table>
      
    <a href="#" class="toggler" detail-num="2">Details</a>
	    
	<a class="detail2" style="display:none">
	    <br>
	    <br>Depart from YVR (Vancouver International Airport) at 5:00 PM
	    <br>Arrive at ICH (Incheon International Airport) at 1:45 PM
	    <br>Layover for 1 hour
	    <br>Depart from ICH (Incheon International Airport) at 2:45 PM
	    <br>Arrive at HKG (Hong Kong International Airport) at 4:24 PM
	</a>
	<br>
	<button type="submit" class="pure-button pure-button-primary">Book my flight</button>
</fieldset>
</form>
</body>  

<?php

include('oci_functions.php');

?>