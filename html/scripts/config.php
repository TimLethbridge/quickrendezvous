<?php
// Copyright: Timothy Lethbridge
// This file is made available subject to the open source license in this repository
//

// To allow for testing on the command line
// This can be deleted when in production

if ($_SERVER['argc'] > 1) {
  for ($clindex=1; $clindex < $_SERVER['argc']; $clindex++) {
    $clarg = explode('=', $_SERVER['argv'][$clindex]);
    $clkey = array_shift($clarg);
    $clvalue = implode('=', $clarg);
    $_REQUEST[$clkey]=$clvalue;
  }
}

// TODO load special text for each queue
if (!isset($_REQUEST["queuename"])) {
  $queuename="default";
}
else 
{
  $queuename=$_REQUEST["queuename"];
  if(!is_dir("queues/".$queuename)) {
    echo($queuename." is not a valid queue.\n");
    exit(-1);
  }
}

?>