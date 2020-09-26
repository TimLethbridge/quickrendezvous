<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//
// This file is used to validate arguments used by makeNewApptSlots,php and makeNewQUeue.php
//
// Arguments checked.
//   startday - 0 for today, 1 (default) for tomorrow etc.
//   numdays - how many days starting at startday to schedule appts for (default 1)
//   addifalready - default 0; 1=add new if there are already some at a time
//   maxtoadd - default infinity - add only at most this many during this run
//   startminute - 540=9 a.m. (default) ; 720=noon; 1020=5 p.m.
//   interval - time in minutes between appointment groups (default 15)
//   numperinterval - number of appts per interval (default 1)
//   endminute - default is 5 p.m. (1020)

// Ensure that the startday parameter has sensible values

if (!isset($_REQUEST["startday"])) {
   $startday=1; 
}
else 
{
  $startday=$_REQUEST["startday"];
  if(!is_numeric($startday)) {
    reporterror(1231,"Startday must be an integer. 0 for today");
  }
}


// Ensure that the numdays parameter is a sensible integer

if (!isset($_REQUEST["numdays"])) {
   $numdays=1; 
}
else 
{
  $numdays=$_REQUEST["numdays"]; 
  if(!is_numeric($numdays)) {
    reporterror(1232,"Numdays must be an integer.");
  }
}


// Ensure that the addifalready parameter is zero or 1
// if 0 then later on we will not add any more appts in this timeslot
// if 1 then we will be able to add additional. Used along with maxtoadd

if (!isset($_REQUEST["addifalready"])) {
  $addifalready=0; 
}
else 
{
  $addifalready=$_REQUEST["addifalready"]; 
  if($addifalready != 1 && $addifalready != 0) $addifalready = 1;
}

// Ensure that maxtoadd is set to something sensible, default very large.
// If set to a small number and addifalready is 0, then this program can be
// run over and over to gradually roll out appointments to avoid a rush

if (!isset($_REQUEST["maxtoadd"])) {
  $maxtoadd=999999999999; 
}
else 
{
  $maxtoadd=$_REQUEST["maxtoadd"]; 
  if(!is_numeric($maxtoadd)) $maxtoadd = 999999999999;
}


// Ensure that the startminute parameter is sensible

if (!isset($_REQUEST["startminute"])) {
  $startminute=540; 
}
else 
{
  $startminute=$_REQUEST["startminute"]; 
  if(!is_numeric($startminute)) {
    reporterror(1241,"Startminute must be an integer. 0 for midnight.");
  }
  if($startminute > 1439) $startminute = 1439;
  if($startminute <0) $startminute = 0;
}

// Ensure that the interval parameter is sensible

if (!isset($_REQUEST["interval"])) {
  $interval=15; 
}
else 
{
  $interval=$_REQUEST["interval"]; 
  if(!is_numeric($interval)) {
    reporterror(1242,"Interval must be an integer.");
    exit(-1);
  }
  if($interval <1) $interval = 1;
  if($interval >720) $interval = 720;

}

// Ensure that the numperinterval parameter is sensible

if (!isset($_REQUEST["numperinterval"])) {
  $numperinterval=1; 
}
else 
{
  $numperinterval=$_REQUEST["numperinterval"]; 
  if(!is_numeric($numperinterval)) {
    reporterror(1243,"numperinterval must be an integer.");
    exit(-1);
  }
  if($numperinterval <1) $numperinterval = 1;
  if($numperinterval >1000) $numperinterval = 1000;
}

// Ensure that the endminute parameter is sensible

if (!isset($_REQUEST["endminute"])) {
  $endminute=1020; 
}
else 
{
  $endminute=$_REQUEST["endminute"]; 
  if(!is_numeric($endminute)) {
    reporterror(1244,"endminute must be an integer.");
    reporterror(-1);
  }
  if($endminute <1) $endminute = 1;
  if($endminute >1440) $endminute = 1440;
}

?>