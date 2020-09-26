<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//
// This generates appointment slots
//
// Arguments:
//   queuename - the name of the queue
//   secretcode - the code for this queue
//   startday - 0 for today, 1 (default) for tomorrow etc.
//   numdays - how many days (default 1)
//   addifalready - default 0; 1=add new if there are already some on day
//   startminute - 540=9 a.m. (default) ; 720=noon; 1020=5 p.m.
//   interval - time in minutes between intervale (default 15)
//   numperinterval - number of appts per interval (default 1)
//   endminute - default is 5 p.m. (1020)
//   tz - time zone (default is America/Toronto)
//
// Set queuename and other features common to all scripts
require_once ("config.php");

if (!isset($_REQUEST["secretcode"])) {
  echo "secret code for the queue must be specified in order to have administrative access to this queue\n";
  exit(-1);
}
else 
{
  $secretcode=$_REQUEST["secretcode"];
  // TODO verify secret code 
}

if (!isset($_REQUEST["startday"])) {
   $startday=1; 
}
else 
{
  $startday=$_REQUEST["startday"];
  if(!is_numeric($startday)) {
    echo ("startday must be an integer. 0 for today\n");
    exit(-1);
  }
}

if (!isset($_REQUEST["numdays"])) {
   $numdays=1; 
}
else 
{
  $numdays=$_REQUEST["numdays"]; 
  if(!is_numeric($numdays)) {
    echo ("numdays must be an integer.\n");
    exit(-1);
  }
}

if (!isset($_REQUEST["addifalready"])) {
  $addifalready=0; 
}
else 
{
  $addifalready=$_REQUEST["addifalready"]; 
  if($addifalready != 1) $addifalready = 1;
}

if (!isset($_REQUEST["startminute"])) {
  $startminute=540; 
}
else 
{
  $startminute=$_REQUEST["startminute"]; 
  if(!is_numeric($startminute)) {
    echo ("startminute must be an integer. 0 for midnight\n");
    exit(-1);
  }
  if($startminute > 1439) $startminute = 1439;
  if($startminute <0) $startminute = 0;
}

if (!isset($_REQUEST["interval"])) {
  $interval=15; 
}
else 
{
  $interval=$_REQUEST["interval"]; 
  if(!is_numeric($interval)) {
    echo ("interval must be an integer.\n");
    exit(-1);
  }
  if($interval <1) $interval = 1;
  if($interval >720) $interval = 720;

}

if (!isset($_REQUEST["numperinterval"])) {
  $numperinterval=1; 
}
else 
{
  $numperinterval=$_REQUEST["numperinterval"]; 
  if(!is_numeric($numperinterval)) {
    echo ("numperinterval must be an integer.\n");
    exit(-1);
  }
  if($numperinterval <1) $numperinterval = 1;
  if($numperinterval >1000) $numperinterval = 1000;
}

if (!isset($_REQUEST["endminute"])) {
  $endminute=1020; 
}
else 
{
  $endminute=$_REQUEST["endminute"]; 
  if(!is_numeric($endminute)) {
    echo ("endminute must be an integer.\n");
    exit(-1);
  }
  if($endminute <1) $endminute = 1;
  if($endminute >1440) $endminute = 1440;

}

// Verify the queue directory has the correct secret cpde

$foundSecretCode=@file_get_contents("queues/".$queuename."/secretcode");
if(!$foundSecretCode || trim($foundSecretCode) != $secretcode) {
  echo("invalid secret code entered to modify this queue as an administrator\n");
  exit(-1);
}

//make the specified appointment files


$currenttime=time();
$thisyear=date("yy");
$thismonth=date("m");
$thisday=date("d");
$midnight=mktime(0,0,0,$thismonth,$thisday,$thisyear);
echo ">".date("yy-m-d-H-i-s-T",$midnight)."\n";
echo ">".date("yy-m-d-H-i-s-T",$currenttime)."\n";


for ($dayoffset = $startday; $dayoffset <= $startday+$numdays-1; $dayoffset++) {
  //The following should do DST correctly
  $thatmidnight=mktime(0,0,0,$thismonth,$thisday+$dayoffset,$thisyear);
  $literaldate=date("yy-m-d",$thatmidnight);

  echo "Will do day ".$dayoffset." lit=".($literaldate)."\n";
  
  for ($minuteoffset = $startminute; $minuteoffset < $endminute; $minuteoffset+=$interval) {

    if($addifalready==1 ||
     sizeof(glob("queues/".$queuename."/apptavail/appt-".
     $literaldate."*"))<2) {
      $timetodo=$thatmidnight+$minuteoffset*60;
      // Don't make appointments within 5 minutes
      if($dayoffset>0 || $timetodo > ($currenttime+5*60)) {
        $literaltime=date("H-i-s-T",$thatmidnight+$minuteoffset*60);
        echo "  Will do min".$minuteoffset." lit ".$literaltime."\n";
      
        // Generate the appointments
      
        // We only do it if addifalready is 1 or there are none

        for($apptcount=1;
         $apptcount <= $numperinterval;
         $apptcount++) {
          $filetocreate=
            "queues/".$queuename."/apptavail/appt-".$literaldate.
            "-".$literaltime."-".randbase36(7);
          echo "will create ".$filetocreate."\n";
          $thefile=fopen($filetocreate,"w");
          fwrite($thefile,$filetocreate);
          fclose($thefile);
        }
      }
    } 
  }
}




echo("reached end OK DEBUG\n");

?>
