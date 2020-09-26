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

?>