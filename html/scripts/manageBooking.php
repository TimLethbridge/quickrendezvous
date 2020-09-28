<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//
// Set queuename and other features common to all scripts
require_once ("config.php");

function getnewappointment($thequeue, $thebookingcode, $theavaillist) {
  // Core function to get first appointment
  // May fail if others have got all of them first
  // Will get a NEW appointment even if the booking code already has one
  // Will be enhanced to allow for booking earlier or later
  asort($theavaillist);
  $didGetAppt=false;
  $theappointment="";
  foreach($theavaillist as $listitem) {
    $filecomponents=explode("/",$listitem);
    $theappointment=$filecomponents[4];
    // This may fail if an appointment gets deleted while we are trying
    // to book it. We will keep trying.
    $didGetAppt = @rename($listitem,
       "../queues/".$thequeue."/apptbooked/".$theappointment."_".$thebookingcode);
    if($didGetAppt) break;
  }
  return $theappointment; // if none obtained, then blank
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Booking management</title>
  <style>
<?php readfile("mainstyles.css"); ?>
</style>  

<?php
if(isset($_REQUEST["queuename"]) && (
  isset($_REQUEST["bookingcode"]) || isset($_POST["bookingcode"])
  ) ) { 

  ?>

<script type="text/javascript" src="lib/qrcode.min.js" />
<script type="text/javascript"> var a=5;  </script>

<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
  //alert("testing it");
    new QRCode(document.getElementById("qrcode"),
    {
      text: "https://quickrendezvous.org/qr/?queuename=<?php echo $_REQUEST["queuename"]?>&lang=<?php echo $lang?>",
      width: 80,
      height:80
    }
  );
 },false);
  
</script>
<?php } ?>

<script>
function validateForm() {
  var firstName, lastName, numPeople, message;

  // Get the value of the input field with id="numb"
  //lastName = document.getElementById("lastname").value;
  lastName = document.forms["apptData"]["lastname"].value;

  firstName = document.getElementById("firstname").value;
  numPeople = document.getElementById("numpeople").value;

  if (lastName.length<2) {
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
  return true;
}
</script>

</head>
<body>
<p><em><font color="orange">Please do not rely on this site yet. It is currently a proof of concept to demonstrate ideas, test and obtain feedback. Some functions are incomplete and there are known bugs. Ne vous fiez pas encore à ce site. Il s'agit actuellement d'une preuve de concept pour démontrer des idées, tester et obtenir des commentaires. Certaines fonctions sont incomplètes et il existe des bogues connus. Please send suggestions to:  Veuillez envoyer vos suggestions à: <a href="mailto:timothy.lethbridge@uottawa.ca">timothy.lethbridge@uottawa.ca</a></font></em></p>

  <span style="float: left">
          <img src="../images/quickrendezvouslogosm.png" style="padding-right: 5px"   height=70 />        
        </span> 


<?php
  echo $floatlangblock;
  // Set empty values
  $firstname="";
  $lastname="";
  $emailaddress="";
  $cellphone="";
  $numpeople=1;
  $bookingcode="";
  $appointment="";

  if (file_exists("../queues/".$queuename."/metadata.json")) {
    $queueMetadataJson=file_get_contents("../queues/".$queuename."/metadata.json");
    $queueMetadata=json_decode($queueMetadataJson,true);
    echo("<h1>".$queueMetadata["queuetitle"]."</h1>");
    echo("<p>".$queueMetadata["description"]."</p>");

  }


  // Analyse the queue (printed at end, but needed for booking)
  $dir_prefix="../";
  require_once ("queuestats.php");

  if ((isset($_POST["bookingcode"]) && !isset($_POST["lastname"]))
     || isset($_REQUEST["bookingcode"]) && !isset($_POST["lastname"])) {
    // Case 1: We came from a form where only bookingcode had been entered to search
    // Load the appointment and information
    // If not found, go back to the main form.
    if(isset($_REQUEST["bookingcode"])) {
      $bookingcode=$_REQUEST["bookingcode"];
    }
    else {
      $bookingcode=$_POST["bookingcode"];    
    }
    $fileToLoad="../queues/".$queuename."/requestors/".$bookingcode.".json";
    if (file_exists($fileToLoad)) {
      $queueMetadataJson=file_get_contents($fileToLoad);
      $requestorMetadata=json_decode($queueMetadataJson,true);
      $firstname=$requestorMetadata["firstname"];
      $lastname=$requestorMetadata["lastname"];
      $emailaddress=$requestorMetadata["emailaddress"];
      $cellphone=$requestorMetadata["cellphone"];
      $numpeople=$requestorMetadata["numpeople"];
      if(isset($requestorMetadata["appointment"])) {
        $appointment=$requestorMetadata["appointment"];
      }
    }
    else { // File does not exist
      echo("<p>Booking code ".$bookingcode." was not found in this queue; please go back to try again. Booking codes are only good for one appointment, so it may be an old one.</p>");
      
      echo "<p><a href=\"../?lang=".$lang."&queuename=".$queuename."\" class=\"button2\" title=\"Click here to back to the page where you can enter your booking code, or request a new one.\">Go back to the page where you can enter your booking code, or request a new one</a></p>";
      exit(0);
    }    
  }
  if (isset($_POST["lastname"])) {
    // Case 2: We came from a form  where data was filled in
    // Attempt to save/update the data, then display it for change
    // We grab a new appointment too, if we don't have one.
    if(!isset($_POST["bookingcode"]) || $_POST["bookingcode"]=="" ) {
      // Case 2a: Data had been freshly entered so needs saving and appt making
      $bookingcode=randbase36(9);

      // Grab the first appointment (if any available)
      // $appointment should be blank
      if($appointment != "") {
        echo("<p>Debug Error: we are about to get a new appointment, but we already have one</p>");
      }
      $appointment=getnewappointment($queuename, $bookingcode, $availlist);
      if($appointment != "") $didGetAppt = true;
    }
    else {
      // Case 2b: We previously had saved a bookincode
      $bookingcode = $_POST["bookingcode"];

    }
    
    // Save the information which may be new or modified
    $lastname=$_POST["lastname"];
    $firstname=$_POST["firstname"];
    $emailaddress=$_POST["emailaddress"];
    $cellphone=$_POST["cellphone"];
    $numpeople=$_POST["numpeople"];
    if($appointment=="") {
      // get previously saved one if any
      $appointment=$_POST["appointment"];
    }

    $requestorMetadata = json_encode(Array (
      "lastname" => $lastname,
      "firstname" => $firstname,
      "emailaddress" => $emailaddress,
      "cellphone" => $cellphone,
      "numpeople" => $numpeople,
      "appointment" => $appointment
    ),JSON_PRETTY_PRINT);
    $filenametowrite="../queues/".$queuename."/requestors/".$bookingcode.".json";
    $thefile = fopen($filenametowrite,"w");
    fwrite($thefile,$requestorMetadata);
    fclose($thefile);
  }
  else {
    // Case 3: We came from a link with nothing yet in main page
    // We have to fill in fresh information
    // We don't have a booking code yet and we have to fill in fresh information
    echo "<p>Use the following to enter data and create an appointment.</p>";
  }
  
  if($bookingcode !="") {
    echo "<p>Your booking code is <b>".$bookingcode."</b> -- Please print this page or write this information down so you can come back later </p>";
  }

  if($appointment !="") {
    $apptdetail=explode("-",$appointment);
    $appttime=mktime(
      $apptdetail[4],$apptdetail[5],$apptdetail[6],
      $apptdetail[2],$apptdetail[3],$apptdetail[1]);    
    $prettyApptTime=transday(date("l",$appttime))." ".
      $apptdetail[1]."-".$apptdetail[2]."-".$apptdetail[2]." at ".
      $apptdetail[4].":".$apptdetail[5].":".$apptdetail[6];
    
    echo "<p>Your appointment is: <font size=\"+2\"> <b>".$prettyApptTime."</b> </font></p>";
  }
  else {
    if($bookingcode!="") {
      echo "<p>We have not been able to make an appointment for you yet. Keep trying using this same booking code.</b></p>";
    }
  }

?>

<b><p id="errormessage" style="color:red"> </p></b>

<form name="apptData" method="post" action="manageBooking.php?queuename=<?php echo($queuename)?>"  onsubmit="return validateForm();">

<label for="lastname"><b>Lastname:</b></label><br/>
<input type="text" id="lastname" name="lastname" value="<?php echo $lastname;?>" title="A name is required. The people managing this queue may cancel your appointment if this is incorrect."></input>
<br/>

<label for="firstname"><b>Firstname:</b></label><br/>
<input type="text" id="firstname" name="firstname" value="<?php echo $firstname;?>" title="The people managing this queue may need your name, and may cancel your appointment if this is absent or incorrect."  ></invput>
<br/>

<label for="emailaddress"><b>Email Address:</b></label><br/>
<input type="text" id="emailaddress" name="emailaddress" value="<?php echo $emailaddress;?>" title="Your email address will be available to the people in charge of this queue and they might contact you. It is possible that your appointment could be cancelled without notifying you if this email address is incorrect or absent." ></input>
<br/>

<label for="cellphone"><b>Cellphone for Texts:</b></label><br/>
<input type="text" id="cellphone" name="cellphone" value="<?php echo $cellphone;?>" title="Your phone number will be available to the people in charge of this queue and they might contact you. This site will be enhanced to enable verification of this number. There may be costs associated with sending texts. It is possible that your appointment could be cancelled without notifying you if this phone number is incorrect or absent."></input>
<br/>

<label for="numpeople"><b>Number of People:</b></label><br/>
<input type="text" id="numpeople" name="numpeople" value="<?php echo $numpeople;?>"></input>
<br/>

<input type="hidden" id="bookingcode" name="bookingcode" value="<?php echo $bookingcode;?>"></input>
<br/>

<input type="hidden" id="appointment" name="appointment" value="<?php echo $appointment;?>"></input>
<br/>


<input type="submit" class="button2" title="Enter or change the information above then click here. Your booking code and appointment will remain unchanged." value="Click here to enter or modify your data">

</input>
</form>

<?php
echo $queueInfoHtml;
?>


<?php
/* FOR DEBUG
echo "<pre>";
print_r(get_defined_vars());
echo "</pre>";
*/

 ?>

</body>
