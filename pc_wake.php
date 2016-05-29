<?php
  session_start();
  if ( $_SESSION["authenticated"] == 'true' ) {
    $device_id = $_GET["device_id"];
    $curr_status = exec("/usr/sbin/etherwake ".$device_id);
    print $curr_status;
  } else {
    print "ERROR: Not authorized";
  }
?>
