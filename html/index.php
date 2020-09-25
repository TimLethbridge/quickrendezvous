<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//
// Set queuename and other features common to all scripts
require_once ("scripts/config.php");
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


</head>
<body>

<h1>Tool to quickly generate appointments first-come-first served fairly: Under development</h1>

<p>This tool is under development, but functional. Currently this is a proof-of-concept to demonstrate a better way to book testing appointments for Covid-19. If all goes well we should be able turn this, or a similar system, live shortly. </p>

<p>Its purpose is to allow you to book an appointment. You need to register your basic information first. Then you will be given a 6-character booking code. You must use this code to book your appointment. You can also use the same code to cancel your appointment (making it available to others), or try to get an earlier appointment, or ask for a later appointment.</p>

<p>If you are not initially given an appointment because they are all full, your booking code will maintain its priority in the queue and will be given an appointment when new ones become available. You should check back to see if one has been made for you, and confirm it. If you do not confirm, it will likely be given to somebody else.</p>

<p>There is a separate administrative process for adding additional appointments, on a rolling basis, so there should be a rush at any particular point in time to obtain an appointment.</p>

<p><b>Start by clicking the following button to obtain a booking code. This code will be valid for making one appointment (for an individual or family)</b></p>

<a class="button2" href="scripts/getBookingCode.php" title="This will ask you for basic information such as your name, and the number of people to attand the appointment. It will then give you a code that you can later use to request an appointment. When you are given the code, make sure you write it down. We will soon enhance this tool so it will also email and text the code to you.">
Register to get a booking code
</a>

<br/>&nbsp;<br/>

<form action="scripts/manageBooking.php">
<label for="bookingcode"><b>If you already have a booking code, enter it here, then click below to request, modify or cancel an appointment:</b></label><br/>
<input type="text" id="bookingcode" na,e="fname"></input>

<br/>

<input type="submit" class="button2" href="scripts/getBookingCode" title="If you have a booking code, enter it above, then click this buttom to request, cancel, or modify an appointment. Your priority will be based on your the first time you click this link." value="Click here to request or modify an appointment.">

</input>
</form>

</body>
