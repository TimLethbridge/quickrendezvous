<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//
// Set queuename and other features common to all scripts
require_once ("config.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Tool to quickly generate appointments first-come-first served fairly: Under development</title>
  <style>
  .button2 {
   border-top: 1px solid #d3bc8f;
   line-height: 1.6;
   background: #d8a695;
   background: -webkit-gradient(linear, left top, left bottom, from(#e5bcae), to(#d8a695));
   background: -webkit-linear-gradient(top, #e5bcae, #d8a695);
   background: -moz-linear-gradient(top, #e5bcae, #d8a695);
   background: -ms-linear-gradient(top, #e5bcae, #d8a695);
   background: -o-linear-gradient(top, #e5bcae, #d8a695);
   padding: 2px 5px;
   -webkit-border-radius: 6px;
   -moz-border-radius: 6px;
   border-radius: 6px;
   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
   box-shadow: rgba(0,0,0,1) 0 1px 0;
   text-shadow: rgba(0,0,0,.4) 0 1px 0;
   color: white;
   font-size: 14px;
   font-family: 'Lucida Grande', Helvetica, Arial, Sans-Serif;
   text-decoration: none;
   vertical-align: middle;
   }
.button2:hover {
   border-top-color: #810b09;
   background: #810b09;
   color: #ccc;
   }
.active {
  background: #C98C7D;
}
</style>  

<script>
function validateForm() {
  var firstName, lastName, numPeople, message;

  // Get the value of the input field with id="numb"
  //lastName = document.getElementById("lastname").value;
  lastName = document.forms["apptData"]["lastname"].value;

  firstName = document.getElementById("firstname").value;
  numPeople = document.getElementById("numpeople").value;

  if (lastName.length<3) {
    message = "The last name must have at least two characters.";
    document.getElementById("errormessage").innerHTML = message;
    return false;
  }

  // If numPeople is Not a Number or less than one or greater than 5
  if (isNaN(numPeople) || numPeople < 1 || numPeople > 5) {
    message = "The number of people must be between 1 and 5.";
    document.getElementById("errormessage").innerHTML = message;
    return false;
  }
}
</script>

</head>
<body>
<?php
  if (file_exists("../queues/".$queuename."/metadata.json")) {
    $queueMetadataJson=file_get_contents("../queues/".$queuename."/metadata.json");
    $queueMetadata=json_decode($queueMetadataJson,true);
    echo("<h1>".$queueMetadata["queuetitle"]."</h1>");
  }

  // Analyse the queue (printed at end, but needed for booking)
  $dir_prefix="../";
  require_once ("queuestats.php");

  if (isset($_POST["bookingcode"])) {
    // Case 1: We came from a form where a bookingcode had been entered to search
    // Load the appointment and information
    // If not found, go back to the main form.
    echo "<p>DEBUG: Will search and fill in the information if found.</p>";
  }
  if (isset($_POST["lastname"])) {
    // Case 2: We came from a form  where data was filled in
    // Attempt to save/update the data, then display it

    $bookingcode=randbase36(9);
    echo "<p>Your booking code is <b>".$bookingcode."</b> -- Please write this information down so you can come back later </p>";
    
    // Grab the first appointment (if any available)
    // First sort so we have them available in time.
    asort($availlist);
    $didGetAppt=false;
    $appointment="";
    foreach($availlist as $listitem) {
      $filecomponents=explode("/",$listitem);
      $appointment=$filecomponents[4];
      $didGetAppt = rename($listitem,
        "../queues/".$queuename."/apptbooked/".$appointment."_".$bookingcode);
      if($didGetAppt) break;
    }
    echo "Got appointment ".$appointment;
    
    // Save the information
    echo "<p>DEBUG: Will save the information.</p>";
    $requestorMetadata = json_encode(Array (
      "lastname" => $_POST["lastname"],
       "firstname" => $_POST["firstname"],
       "emailaddress" => $_POST["emailaddress"],
       "cellphone" => $_POST["cellphone"],
       "numpeople" => $_POST["numpeople"],
       "appointment" => $appointment
     ),JSON_PRETTY_PRINT);
    $thefile =
      fopen("../queues/".$queuename."/requestors/".$bookingcode.".json","w");
    fwrite($thefile,$requestorMetadata);
    fclose($thefile);
  }
  else {
    // Case 3: We came from a link with nothing yet in main page
    // We have to fill in fresh information
    // We don't have a booking code yet and we have to fill in fresh information
    echo "<p>Use the following to enter data and create an appointment.</p>";
  }

?>

<b><p id="errormessage" style="color:red"> </p></b>

<form name="apptData" method="post" action="manageBooking.php?queuename=<?php echo($queuename)?>"  onsubmit="return validateForm()">

<label for="lastname"><b>Lastname:</b></label><br/>
<input type="text" id="lastname" name="lastname" ></input>
<br/>

<label for="firstname"><b>Firstname:</b></label><br/>
<input type="text" id="firstname" name="firstname" ></input>
<br/>

<label for="emailaddress"><b>Email Address:</b></label><br/>
<input type="text" id="emailaddress" name="emailaddress"></input>
<br/>

<label for="cellphone"><b>Cellphone for Texts:</b></label><br/>
<input type="text" id="cellphone" name="cellphone"></input>
<br/>

<label for="numpeople"><b>Number of People:</b></label><br/>
<input type="text" id="numpeople" name="numpeople"></input>
<br/>


<input type="submit" class="button2" title="Enter or change the information above then click here to change your information." value="Click here to enter or modify your data.">

</input>
</form>

<?php
echo $queueInfoHtml;
?>

<p>Test</p>

<?php

echo "<pre>";
print_r(get_defined_vars());
echo "</pre>";
 ?>

</body>
