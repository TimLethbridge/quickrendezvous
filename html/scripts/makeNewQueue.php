<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//
// This creates a new queue. Anyone is allowed to make queues.
// Each queue has a name, and will be identified by a generated secret code
// That must then be used to modify the queue later
//
// Arguments: (all but queuename can be changed later).
//   queuename - alphanumeric - the queue to create ... must not already exist
//   queuetitle - String to display to users of the queue
//   description - Encoded text to display to users describing the queue
//   logoURL - Optional URL to display for the queue's organization
//   orgURL - Link to the origanization to get other information
//
// Arguments also used by makeNewApptSlots.php
//   startday - 0 for today, 1 (default) for tomorrow etc.
//   numdays - how many days starting at startday to schedule appts for (default 1)
//   maxtoadd - default infinity - repeatedly add this many max every few minutes
//   startminute - 540=9 a.m. (default) ; 720=noon; 1020=5 p.m.
//   interval - time in minutes between appointment groups (default 15)
//   numperinterval - number of appts per interval (default 1)
//   endminute - default is 5 p.m. (1020)
//
//   tz - time zone (default is America/Toronto)
//
// Ensure there is no error due to queue not existing
$willCreateQueue=true;

// Set queuename and other features common to all scripts
require_once ("config.php");

// Validate that the queuename is reasonable and does not already exist

// It must be alphanumeric
if(!ctype_alnum($queuename)) { 
  reporterror(212,"Queuename ".$queuename."  requested is not alphanumeric.");
}

if(file_exists("../queues/".$queuename)) {
  reporterror(213,"Queuename ".$queuename." already exists.");
}

// If the following are wrong, the site will not work
// and they will have to be changed, but we leave this to the user

$queuetitle="Queue called ".$queuename;
$queuetitlefr="File d'attente qui s'apelle ".$queuename;

$description="This is a new queue called ".$queuename." that has not been given a description yet. Users can still try to request appointments on the site. The administrator can change this description to change its purpose.";
$descriptionfr="Ceci est une nouvelle file d'attente appelée".$queuename." qui n'a pas encore reçu de description Français. Les utilisateurs peuvent toujours essayer de demander des rendez-vous sur le site. L'administrateur peut modifier cette description pour changer son objectif."

$logoURL="";
$orgURL="";

if (isset($_REQUEST["queuetitle"])) {
  $queuetitle=$_REQUEST["queuetitle"];
}
if (isset($_REQUEST["queuetitlefr"])) {
  $queuetitle=$_REQUEST["queuetitlefr"];
}

if (isset($_REQUEST["description"])) {
  $description=$_REQUEST["description"];
}
if (isset($_REQUEST["descriptionfr"])) {
  $description=$_REQUEST["descriptionfr"];
}

if (isset($_REQUEST["logoURL"])) {
  $logoURL=$_REQUEST["logoURL"];
}
if (isset($_REQUEST["orgURL"])) {
  $orgURL=$_REQUEST["orgURL"];
}

// validate other arguments
require_once ("checkpatternargs.php");

// Generate json for the data

$queueMetadata = json_encode(Array (
  "queuetitle" => $queuetitle,
  "queuetitle-fr" => $queuetitlefr,
  "description" => $description,
  "description-fr" => $descriptionfr,
  "logoURL" => $logoURL,
  "orgURL" => $orgURL
),JSON_PRETTY_PRINT);

// Clone the template directory and put the json file in the directory

// TODO this might fail - do something if it does
$didsucceed=mkdir("../queues/".$queuename,0777,true);

$thefile = fopen("../queues/".$queuename."/metadata.json","w");
fwrite($thefile,$queueMetadata);
fclose($thefile);

copy("../queues/template/index.html",
  "../queues/".$queuename."/index.html");

$didsucceed=mkdir("../queues/".$queuename."/apptavail",0777,true);
copy("../queues/template/apptavail/index.html",
  "../queues/".$queuename."/apptavail/index.html");

$didsucceed=mkdir("../queues/".$queuename."/apptbooked",0777,true);
copy("../queues/template/apptbooked/index.html",
  "../queues/".$queuename."/apptbooked/index.html");

$didsucceed=mkdir("../queues/".$queuename."/requestors",0777,true);
copy("../queues/template/requestors/index.html",
  "../queues/".$queuename."/requestors/index.html");

$secretCodeToUse=randbase36(9);
$thefile = fopen("../queues/".$queuename."/secretcode","w");
fwrite($thefile,$secretCodeToUse);
fclose($thefile);




echo(" The SecretCode to use to access this queue is ".$secretCodeToUse)." Give this URL to your participants";


?>







