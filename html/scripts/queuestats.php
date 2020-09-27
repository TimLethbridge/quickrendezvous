<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//
// This outputs stats in html about the queue in $queuename

$availlist=glob($dir_prefix."queues/".$queuename."/apptavail/appt-*");

$thecurrent=time();
$allapptcount=0;
$next3hourscount=0;
$next8hourscount=0;
$next24hourscount=0;
$next48hourscount=0;
$earliesttime="No appointments available";
$earlieststamp=999999999999999;
$latesttime="";
$lateststamp=0;

foreach($availlist as $listitem) {
  $apptdetail=explode("-",$listitem);
  $appttime=mktime(
    $apptdetail[4],$apptdetail[5],$apptdetail[6],
    $apptdetail[2],$apptdetail[3],$apptdetail[1]);

  if($appttime <= $thecurrent) {
    // It was old, so delete it
    unlink($listitem);
  }
  else
  {
    $allapptcount++;
    if($appttime < ($thecurrent+3*60*60)) $next3hourscount++;
    if($appttime < ($thecurrent+8*60*60)) $next8hourscount++;
    if($appttime < ($thecurrent+24*60*60)) $next24hourscount++;
    if($appttime < ($thecurrent+48*60*60)) $next48hourscount++;
    if($appttime > $lateststamp) {
      $lateststamp = $appttime;
      $latesttime = "Latest appointment currently available to book: ".
        date("l",$appttime)." ".
        $apptdetail[1]."-".$apptdetail[2]."-".$apptdetail[3]." ".
        $apptdetail[4].":".$apptdetail[5].":".$apptdetail[6];
    }
    if($appttime < $earlieststamp) {
      $earlieststamp = $appttime;
      // TODO date function below outputs in English only; must manually translate
      $earliesttime = "Earliest appointment currently available: ".
        date("l",$appttime)." ".
        $apptdetail[1]."-".$apptdetail[2]."-".$apptdetail[3]." ".
        $apptdetail[4].":".$apptdetail[5].":".$apptdetail[6];
    }
  }
}

ob_start();

?>

<br/>
<h2>Current information about queue <?php echo $queuename; ?></h2>

<p><?php echo $earliesttime ?>

<p><?php echo $next3hourscount ?>
 appointments available in the next 3 hours.</p>

<p><?php echo $next8hourscount-$next3hourscount ?>
 appointments available between 3 and 8 hours from now.</p>

<p><?php echo $next24hourscount-$next8hourscount ?>
 appointments available between 8 and 24 hours from now.</p>

<p><?php echo $next48hourscount-$next24hourscount ?>
 appointments available between 24 and 48 hours from now.</p>

<p><?php echo $allapptcount-$next48hourscount ?>
 appointments currently accepting bookings after 48 hours from now.</p>


<p><?php echo $latesttime ?>

<?php
  $queueInfoHtml=ob_get_contents();
  ob_end_clean();
 ?>




