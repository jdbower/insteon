<?php
  session_start();
  if ( $_SESSION["authenticated"] == 'true' ) {
    $device_id = $_GET["device_id"];
    $value = $_GET["value"];
    $curr_status = exec("./insteon ".$device_id." on ".$value);
    print $curr_status;
  } else {
    print "ERROR: Not authorized";
  }
?>
