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
  <title>Create a new queue</title>
  <style>
<?php readfile("mainstyles.css"); ?>
</style>  

<script>
function validateForm() {
   // This will be added
   return(true);
}
</script>

</head>
<body>
<p><em><font color="orange">Please do not rely on this site yet. It is currently a proof of concept to demonstrate ideas, test and obtain feedback.</font></em></p>

<h2>Queues currently can be created by the administrator of this site. The ability to create queues here will be added very soon.</h2>

<p>This page may work, but it is still under development<p>

<p>Although anyone can create a queue, you are asked to do it responsibly. Distribute the queue only to people who might reasonably want to book an appointment.</p>

<p>You will be asked to provide a name and description for the queue. It can be for such tasks as medical testing, vaccinations, appointments with a teacher, or access to a recreational or commercial facility where the number of visitors must be limited.<p>

<p>When the queue is created you will be given a link you can send to people. You will also be given a QR code that you can post. You will be responsible for adding new appointments to the queue.<p>



</body>
</html>