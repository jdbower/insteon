<?php
  session_start();
  if ( $_SESSION["authenticated"] == 'true' ) {
    $device_id = strtolower($_GET["device_id"]);
    $ip = exec("arp | grep $device_id | awk '{print $1}'");
    if ( $ip == "" ) {
      $ip = exec("nmap -sn --min-parallelism 255 --max-retries 0 $(route | grep \* | awk '{print $1}' | tail -n1)/24 > /dev/null && arp | grep $device_id");
    }
    if ( $ip == "" ) {
      $curr_status = 0;
    } else {
      $tmp = exec("ping -c 1 -W 1 $ip", $out, $return);
      if ( $return == "00" ) {
        $curr_status = 1;
      } else {
        $curr_status = 0;
      }
    }
    print $curr_status;
  } else {
    print "ERROR: Not authorized";
  }
?>
