<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//

// This file contains general utillities that are used by other php files

// The following allows for testing on the command line
// This can be deleted when in production when arguments would come from 
// variables sent through URLs.

global $iscommandline;
$iscommandline=false;
if (isset($_SERVER["argc"])) {
  $iscommandline=true;
  if ($_SERVER['argc'] > 1) {
    for ($clindex=1; $clindex < $_SERVER['argc']; $clindex++) {
      $clarg = explode('=', $_SERVER['argv'][$clindex]);
      $clkey = array_shift($clarg);
      $clvalue = implode('=', $clarg);
      $_REQUEST[$clkey]=$clvalue;
    }
  }
}

// Default timezone. Can be overridden by tz arguments
// Silent in the case of errors
// Arguments such as UTC, EDT, and America/Chicago work
// The correct timezone is needed for local installation
date_default_timezone_set("America/Toronto");

if (isset($_REQUEST["tz"])) {
  @date_default_timezone_set($_REQUEST["tz"]);
}

// Default language is English. But we set the lang variable to allow other language

global $lang;
if (isset($_REQUEST["lang"])) {
  $lang=$_REQUEST["lang"];
}
else {
  $lang="en";
}
if($lang!="en") {
  $altlang="en";
  $altlangword="English";
}
else { // if English the alternative is French
  $altlang="fr";
  $altlangword="Français";
}

// If the incoming query had a language argument, we get rid of it
$otherargs=str_replace("&lang=".$lang,"",$_SERVER["QUERY_STRING"]);
$otherargs=str_replace("lang=".$lang."&","",$otherargs);
$otherargs=str_replace("lang=".$lang,"",$otherargs);

$floatlangblock = "\n
  <span style=\"float: right\">\n
    <a href=\"?lang=".$altlang."&".$otherargs."\">".$altlangword."</a>\n
    <span   id=\"qrcode\"> </span>\n
  </span>\n";


// Decide what to do if there is an error in a parameter or in the data
//  Arguments: 
//    errnum: 100-999 are problems with user arguments. 1000+ are system faults
//    thetext: English only for now. For multilingual operation translate via errnum
function reporterror($errnum,$thetext) {
  // TODO. Currently just reports the error and exits
  // TODO. Need to be able to present sensible html back to the user
  global $iscommandline;
  if($iscommandline) {
    echo("Error ".$errnum.": ".$thetext."\n");
    exit(-1);
  }
  else {
    // some errors are in the argument
    if($errnum < 110) {
      // We haven't failed in any action ... just display the argument error
      $_REQUEST["errorToDisplay"]=$thetext;
    }
    else {
      // TODO make errors appear on the main page. For now we will just display
      echo("Error ".$errnum.": ".$thetext."\n");
      exit(-1);

      // TODO We need to take the user to a new error reporting page with the error
      ob_start();
      header('Location: '.$_SERVER['REQUEST_URI']."&errorToDisplay=".$thetext);
      ob_end_flush();
      exit(0);
    }
  }
}


// Ensure we are working with the expected queue
// All functions work on a queue.
// TODO load special text defined for the given queue
// to tailor the main screen
if(isSet($willCreateQueue)) {
  // We areintending to create a queue
  if (!isset($_REQUEST["queuename"])) {
    reporterror(201,"A queue name must be specified when creating a queue");
  }
  $queuename=$_REQUEST["queuename"];
}
else {
  // We are intending to work with an existing queue
  if (!isset($_REQUEST["queuename"])) {
    $queuename="default";
  }
  else 
  {
    $queuename=$_REQUEST["queuename"];
    if(!is_dir("queues/".$queuename)) {
      reporterror(100,$queuename." is not a valid queue. Please check with the person who asked you to visit this site.");
    }
    if($queuename == "template") {
      reporterror(101,"The queue called ".$queuename." cannot be modified by anyone.");
    }
  }
}

// Generates text with characters 0-9 or a-z for unguessable values
// These values are used for queue secret codes, account files and appointments
// so people cannot easily hack.
// Using length=6 would give 36^6 possible combinations = 2 billion combinations
// Using length=9 gives  100 trillion combinations
function randbase36($length) {
  return(substr(
    base_convert(rand(0,999999999).rand(0,9999999999),10,36),
    1,
    $length));
}

function trans($transkey) {
  global $lang;
  $transtab = array (
    "bookin" => array (
      "en" => "Book an appointment in queue",
      "fr" => "Obtenir un rendez-vous dans la file d'attente"
    ),
    "startby" => array (
      "en" => "Start by clicking the following button to obtain a booking code. This code will be valid for making one appointment for an individual or family",
      "fr" => "Commencez par cliquer sur le bouton suivant pour obtenir un code de réservation. Ce code sera valable pour faire un rendez-vous pour une personne ou une famille"
    ),
    "register" => array (
      "en" => "Register to get a booking code",
      "fr" => "Inscrivez pour obtenir un code de réservation"
    ),
    "registerhelp" => array (
      "en" => "This will ask you for basic information such as your name, and the number of people to attand the appointment. It will then give you a code that you can later use to request an appointment. When you are given the code, make sure you write it down. We will soon enhance this tool so it will also email and text the code to you.",
      "fr" => "Cela vous demandera des informations de base telles que votre nom et le nombre de personnes qui assisteront au rendez-vous. Il vous donnera alors un code que vous devrez utiliser ultérieurement pour demander un rendez-vous. Lorsque vous recevez le code, assurez-vous de le noter. Nous allons bientôt améliorer cet outil afin qu'il vous envoie également le code par courrier électronique et par SMS."
    ),
    "already" => array (
      "en" => "If you already have a booking code, enter it here, then click below to request, modify or cancel an appointment:",
      "fr" => "Si vous avez déjà un code de réservation, entrez ici, puis cliquez ci-dessous pour demander, modifier ou annuler un rendez-vous:"
    ),
    "alreadyhelp" => array (
      "en" => "If you have a booking code, enter it above, then click this buttom to request, cancel, or modify an appointment. Your priority will be based on your the first time you click this link.",
      "fr" => "Si vous avez un code de réservation, entrez-le ci-dessus, puis cliquez sur ce bouton pour demander, annuler ou modifier un rendez-vous. Votre priorité sera basée sur votre première fois que vous cliquez sur ce lien."
    ),
    "alreadybutton" => array (
      "en" => "Click here to request or modify an appointment",
      "fr" => "Cliquez ici pour demander ou modifier un rendez-vous"
    ),
    "currentinfo" => array (
      "en" => "Current information about this queue",
      "fr" => "État actuel de cette file d'attente"
    ),
    "avinnext" => array (
      "en" => "appointments available in the next %1 hours",
      "fr" => "rendez-vous disponibles dans les %1 prochaines heures"
    ),
    "avrange" => array (
      "en" => "appointments available between %1 and %2 hours from now.",
      "fr" => "rendez-vous disponibles entre %1 et %2 heures à partir de maintenant"
    ),
    "after48" => array (
      "en" => "appointments currently accepting bookings after %1 hours from now",
      "fr" => "rendez-vous acceptant actuellement les réservations après %1 heures"
    ),
    "earliest" => array (
      "en" => "Earliest appointment currently available",
      "fr" => "Premier rendez-vous actuellement disponible"
    ),
    "latest" => array (
      "en" => "Latest appointment currently available to book",
      "fr" => "Dernier rendez-vous actuellement disponible pour réserver"
    ),
    "noneavail" => array (
      "en" => "No appointments available",
      "fr" => "Aucun rendez-vous n'est actuellement disponible"
    ),
    "tt" => array (
      "en" => "xxxEN",
      "fr" => "xxxFR"
    ),
    "ff" => array (
      "en" => "xxxEN",
      "fr" => "xxxFR"
    ),

    "jjj" => array (
      "en" => "xxxEN",
      "fr" => "xxxFR"
    )

  );
  return($transtab[$transkey][$lang]);
}

function trans1($transkey,$arg1) {
  return str_replace("%1",$arg1,trans($transkey));
}

function trans2($transkey,$arg1,$arg2) {
  return str_replace("%2",$arg2,trans1($transkey,$arg1));
}

function transday($englishday) {
  global $lang;
  if($lang!="fr") return($englishday);
  $dowk= array (
    "Sunday" => "Dimanche",
    "Monday" => "Lundi",
    "Tuesday" => "Mardi",
    "Wednesday" => "Mercredi",
    "Thursday" => "Jeudi",
    "Friday" => "Vendredi",
    "Saturday" => "Samedi"
  );
  return $dowk[$englishday];
}


?>