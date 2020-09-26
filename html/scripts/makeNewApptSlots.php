<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//
// This generates appointment slots. It is used by the administrator
// It can be used to create appointments many days in advance
// It can be used to add additional new appointments using addifallready=1
// Recommended use is to add appointments a few days out, in small groups
// then to add a few more on the current day to accomodate urgent cases
//
// Arguments:
//   queuename - the name of the queue
//   secretcode - the code for this queue as originally set up when the queue created
//
// Arguments processed by checkpatternargs.php
//   startday - 0 for today, 1 (default) for tomorrow etc.
//   numdays - how many days starting at startday to schedule appts for (default 1)
//   addifalready - default 0; 1=add new if there are already some at a time
//   maxtoadd - default infinity - add only at most this many during this run
//   startminute - 540=9 a.m. (default) ; 720=noon; 1020=5 p.m.
//   interval - time in minutes between appointment groups (default 15)
//   numperinterval - number of appts per interval (default 1)
//   endminute - default is 5 p.m. (1020)
//
//   tz - time zone (default is America/Toronto)
//
// Set queuename and other features common to all scripts
require_once ("config.php");

// Verify that the secret code exists, or this administrative action
// cannot be taken.
// Checking of code correctness is done later
// TODO increase security by requiring prior login, but this should be pretty good

if (!isset($_REQUEST["secretcode"])) {
  reporterror(210,"Secret code for the queue must be specified in order to have administrative access to this queue.");
}
else 
{
  $secretcode=$_REQUEST["secretcode"];
}

// validate other arguments
require_once ("checkpatternargs.php");

// Verify the queue directory has the correct secret cpde

$foundSecretCode=@file_get_contents("queues/".$queuename."/secretcode");
if(!$foundSecretCode || trim($foundSecretCode) != $secretcode) {
  reporterror(211,"Invalid secret code entered to modify this queue as an administrator");
}

//make the specified appointment files

$currenttime=time();
$thisyear=date("yy");
$thismonth=date("m");
$thisday=date("d");
$midnight=mktime(0,0,0,$thismonth,$thisday,$thisyear);
$totalapptsadded=0; // keep track to ensure we are not exceeding max

for ($dayoffset = $startday; $dayoffset <= $startday+$numdays-1; $dayoffset++) {
  //The following should do DST correctly
  $thatmidnight=mktime(0,0,0,$thismonth,$thisday+$dayoffset,$thisyear);
  $literaldate=date("yy-m-d",$thatmidnight);

  // echo "DEBUG Will do day ".$dayoffset." lit=".($literaldate)."\n";
  
  for ($minuteoffset = $startminute; $minuteoffset < $endminute; $minuteoffset+=$interval) {
     
    $timetodo=$thatmidnight+$minuteoffset*60;
    // Don't make appointments within 5 minutes
    if($dayoffset>0 || $timetodo > ($currenttime+5*60)) {
      $literaltime=date("H-i-s-T",$thatmidnight+$minuteoffset*60);
      // echo "  DEBUG Will do min".$minuteoffset." lit ".$literaltime."\n";
      
      // Generate the appointments
      
      // We only do it if addifalready is 1 or there are none
      // Check whether we already have appointments at this time and check
      // if so that we are allowing to add more by addifalready = 1
      if($addifalready==1 ||
       sizeof(glob("queues/".$queuename."/appt*/appt-".
       $literaldate."-".$literaltime."-"."*"))==0) {

        for($apptcount=1;
         $apptcount <= $numperinterval && $totalapptsadded<$maxtoadd;
         $apptcount++) {
          $filetocreate=
            "queues/".$queuename."/apptavail/appt-".$literaldate.
            "-".$literaltime."-".randbase36(7);
          echo "DEBUG will create ".$filetocreate."\n";
          $thefile=fopen($filetocreate,"w");
          fwrite($thefile,$filetocreate);
          fclose($thefile);
          $totalapptsadded++;
        }
      }
    } 
  }
}




echo("reached end OK DEBUG\n");

?>
