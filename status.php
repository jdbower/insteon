<?php
  session_start();
  if ( $_SESSION["authenticated"] == 'true' ) {
    $device_id = $_GET["device_id"];
    $curr_status = exec("./insteon ".$device_id." status");
    print $curr_status;
  } else {
    print "ERROR: Not authorized";
  }
?>
