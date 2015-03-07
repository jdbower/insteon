<?php
  session_start();
  if ( $_SESSION["authenticated"] == 'true' ) {
    $device_id = $_GET["device_id"];
    $value = $_GET["value"];
    $curr_status = exec("./insteon ".$device_id." fan ".$value);
    # Grab a spurious light status to clear the buffer.
    $light_status = exec("./insteon ".$device_id." status");
    print $curr_status;
  } else {
    print "ERROR: Not authorized";
  }
?>
