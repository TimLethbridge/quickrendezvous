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
<?php readfile("scripts/mainstyles.css"); ?>
</style>  

<?php
if(isset($_REQUEST["queuename"])) { ?>

<script type="text/javascript" src="lib/qrcode.min.js" />
<script type="text/javascript"> alert(test);  </script>

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

</head>
<body>

<?php
  
  if(!isset($_REQUEST["queuename"])) {
    // Display intro page
    if(isset($_REQUEST["lang"]) && $_REQUEST["lang"] == "fr") {
      readfile("rawIntro-fr.html");
    }
    else {
      readfile("rawIntro.html");
    }
    echo ("</body>");
    exit(0);
  }


  echo $floatlangblock;
?>

<?php
  if (file_exists("queues/".$queuename."/metadata.json")) {
    $queueMetadataJson=file_get_contents("queues/".$queuename."/metadata.json");
    $queueMetadata=json_decode($queueMetadataJson,true);

    if($lang=="fr" && isset($queueMetadata["queuetitle-fr"])) {
      echo("<h1>".$queueMetadata["queuetitle-fr"]."</h1>");
    }
    else {
      echo("<h1>".$queueMetadata["queuetitle"]."</h1>");
    }

    if($lang=="fr" && isset($queueMetadata["description-fr"])) {
      echo("<p>".$queueMetadata["description-fr"]."</p>");
    }
    else {
      echo("<p>".$queueMetadata["description"]."</p>");
    }
  }
?>

<h2><?php echo trans("bookin").": ".$queuename ?></h2>

<?php
  if (isset($_REQUEST["errorToDisplay"])) {
    echo("<b><p style=\"color:red\">".$_REQUEST["errorToDisplay"]."</p></b>");
  }
?>

<p><b><?php echo(trans("startby"));?></b></p>

<a class="button2" href="scripts/manageBooking.php?queuename=<?php echo($queuename);?>&lang=<?php echo($lang);?>" title="<?php echo(trans("registerhelp"));?>bb me">
<?php echo(trans("register"));?>
</a>

<br/>&nbsp;<br/>

<form method="post" action="scripts/manageBooking.php?queuename=<?php echo($queuename)?>">
<label for="bookingcode"><b><?php echo trans("already");?></b></label><br/>
<input type="text" id="bookingcode" name="bookingcode"></input>

<br/>

<input type="submit" class="button2" title="<?php echo trans("alreadyhelp");?>" value="<?php echo trans("alreadybutton");?>">

</input>
</form>

<?php
$dir_prefix="";
require_once ("scripts/queuestats.php");
echo $queueInfoHtml;
?>


</body>
