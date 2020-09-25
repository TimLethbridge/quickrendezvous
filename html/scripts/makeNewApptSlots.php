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
}

if (!isset($_REQUEST["numdays"])) {
   $numdays=1; 
}
else 
{
  $numdays=$_REQUEST["numdays"]; 
}

if (!isset($_REQUEST["addifalready"])) {
  $addifalready=0; 
}
else 
{
  $addifalready=$_REQUEST["addifalready"]; 
}

if (!isset($_REQUEST["startminute"])) {
  $startminute=540; 
}
else 
{
  $startminute=$_REQUEST["startminute"]; 
}

if (!isset($_REQUEST["interval"])) {
  $interval=15; 
}
else 
{
  $interval=$_REQUEST["interval"]; 
}

if (!isset($_REQUEST["numperinterval"])) {
  $numperinterval=1; 
}
else 
{
  $numperinterval=$_REQUEST["numperinterval"]; 
}

if (!isset($_REQUEST["endminute"])) {
  $endminute=1020; 
}
else 
{
  $endminute=$_REQUEST["endminute"]; 
}

// Verify there is a directory





var_dump( get_defined_vars() );

?>
