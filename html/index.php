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
<script type="text/javascript"> var a=5;  </script>

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
<p><em><font color="orange">Please do not rely on this site yet. It is currently a proof of concept to demonstrate ideas, test and obtain feedback. Some functions are incomplete and there are known bugs. Ne vous fiez pas encore à ce site. Il s'agit actuellement d'une preuve de concept pour démontrer des idées, tester et obtenir des commentaires. Certaines fonctions sont incomplètes et il existe des bogues connus. Please send suggestions to:  Veuillez envoyer vos suggestions à: <a href="mailto:timothy.lethbridge@uottawa.ca">timothy.lethbridge@uottawa.ca</a></font></em></p>


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

  <span style="float: left">
          <img src="images/quickrendezvouslogosm.png" style="padding-right: 5px"   height=70 />        
        </span> 

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
