<?php

session_start();
if ( $_SESSION["authenticated"] != 'true' ) {
  header( 'Location: index.php' ); 
  exit("ERROR: Not authorized");
}

$ini_array = parse_ini_file("/etc/insteon.ini", true);

print "
<head>
<script src='insteon.js' type='text/javascript'></script>
<link rel='stylesheet' type='text/css' href='insteon.css'>
<link rel='shortcut icon' href='favicon.ico' />

<title>INSTEON Controller</title>
</head>
<center><h1>Welcome to the INSTEON Controller!</h1></center>\n\n";  

$device_list = $ini_array["devices"];

print "<table class='icon-table'>
  <tr>\n";

function draw_device($dev) {
  print "    <td align='center'>\n";
  print "      <div id='".$dev[id]."' name='device_div' style='position: relative; left: 0; top: 0;'>\n";
  if ( $dev[fan] == 'true' ) {
    print "        <img id='".$dev[id]."_icon' class='main-icon' src='fan-off.png' />\n";
    print "        <img id='".$dev[id]."_fan-level' class='fan-level-icon' src='fan-medium.png' />\n";
    print "        <img id='".$dev[id]."_light-on' class='fan-light-icon' src='light-on.png' />\n";
    if ( $dev[dimmer] == 'true' ) {
      print "        <div id='".$dev[id]."_level' class='fan-light-level' ><b></b></div>\n";
    }
  } elseif ( $dev[pc] == 'true' ) {
    print "        <img id='".$dev[id]."_icon' class='main-icon' src='pc-off.png' />\n";
    print "        <img id='".$dev[id]."_light-on' class='pc-icon' src='pc-on.png' />\n";
  } else {
    print "        <img id='".$dev[id]."_icon' class='main-icon' src='light-off.png' />\n";
    print "        <img id='".$dev[id]."_light-on' class='light-icon' src='light-on.png' />\n";
    if ( $dev[dimmer] == 'true' ) {
      print "        <div id='".$dev[id]."_level' class='light-level' ><b></b></div>";
    }
  }
  print "        <img id='".$dev[id]."_reload' class='reload-icon' src='reload.png' title='Click here to refresh device status' onClick='reloadStatus(\"".$dev[id]."\")'/>\n";
  print "        <img id='".$dev[id]."_power' class='power-icon' src='power.png' title='Click here to show the remote' onClick='showRemote(\"".$dev[id]."\")'/>\n";
  print "        <img id='".$dev[id]."_loading' class='loading-icon' src='loading.gif' title='Refreshing device status' />\n";
  print "        <img id='".$dev[id]."_error' class='error-icon' src='error.png' />\n";
  print "      </div>\n";
  print "      <div style='display: none'>\n";
  print "        <iframe id='".$dev[id]."_status' src='' onLoad='getStatus(\"".$dev[id]."\")'></iframe>\n";
  print "        <iframe id='".$dev[id]."_fan-status' src='' onLoad='getFanStatus(\"".$dev[id]."\")'></iframe>\n";
  print "        <div id='".$dev[id]."_off-when-away' title='".$dev[off-when-away]."'></div>\n";
  print "      </div>\n";
  print "      <h3>".$dev[name]."</h3>\n";
  print "    </td>\n";
}

$dev_count=0;
$dev_per_row=4;

foreach ($device_list as $device_name => $curr_device) {
  $device_array = str_getcsv($curr_device);
  $device_id = $device_array[0];
  $device[$device_id][name] = $device_name;
  $device[$device_id][id] = $device_array[0];
  $device[$device_id][details] = $device_array[1];
  if (substr($device[$device_id][details],0,3) == 'x01') {
    $device[$device_id][dimmer] = true;
  } else {
    $device[$device_id][dimmer] = false;
  }
  $device[$device_id][name] = $device_array[2];
  $device[$device_id][off-when-away] = $device_array[3];
  $device[$device_id][room] = $device_array[4];
  # The first two bytes are the product ID, but we can ignore the last
  # as it seems to be a revision.
  if ( substr($device[$device_id][details],0,6) == 'x01x2E' ) {
    $device[$device_id][fan] = 'true';
  } else {
    $device[$device_id][fan] = 'false';
  }
  if ( $device[$device_id][details] == 'pc' ) {
    $device[$device_id][pc] = 'true';
  } else {
    $device[$device_id][pc] = 'false';
  }
  if ( $device[$device_id][room] != $old_room ) {
    $old_room = $device[$device_id][room];
    $room_name = $ini_array[rooms][$old_room];
    print "  </tr>
  <tr>
    <th colspan='2' class='room-header' id='".$old_room."_header'>$room_name</th>
    <th colspan='2' class='temp-header' id='".$old_room."_temp_header'>
      <div class='temp-div' id='".$old_room."_temp'></div>
      <img class='temp-icon' id='".$old_room."_cool' title='Cooling' src='hvac-cool.png'>
      <img class='temp-icon' id='".$old_room."_heat' title='Heating' src='hvac-heat.png'>
      <img class='temp-icon' id='".$old_room."_fan' title='Fan is on' src='hvac-fan.png'>
      <img class='temp-icon' id='".$old_room."_away' title='Set to Away' src='away.png'>
      <img class='temp-icon' name='nest_power' id='".$old_room."_power' src='power.png' onClick='showNestRemote(\"".$old_room."\")'>
      <img class='temp-icon' name='nest_loading' id='".$old_room."_loading' src='loading.gif')'>
    </th>\n";
    $dev_count = 0;
  }
  if ( $dev_count % $dev_per_row == 0 ) {
    print "  </tr>
  <tr>\n";
  }
  $dev_count++;
  draw_device($device[$device_id]);
}
print "  </tr>
</table>";

print "
<div id='fan_remote' class='remote-div' onClick='hideRemote()'>
<table border='1' class='remote-table'>
<tr><td id='fan_remote-light-on' colspan='2' class='fan_remote-button remote-not-selected'>On</td></tr>
<tr><td id='fan_remote-fan-high' class='fan_remote-small-button remote-not-selected'>High</td>
<td id='fan_remote-fan-medium' class='fan_remote-small-button remote-not-selected'>Med</td>
<tr><td id='fan_remote-fan-low' class='fan_remote-small-button remote-not-selected'>Low</td>
<td id='fan_remote-fan-off' class='fan_remote-small-button remote-not-selected'>Off</td></tr>
<tr><td id='fan_remote-light-off' colspan='2' class='fan_remote-button remote-not-selected'>Off</td></tr>
</table>
</div>
<div id='light_remote' class='remote-div' onClick='hideRemote()'>
<table border='1' class='remote-table'>
<tr><td id='light_remote-light-on' colspan='2' class='light_remote-button remote-not-selected'>On</td></tr>
<tr><td id='light_remote-light-off' colspan='2' class='light_remote-button remote-not-selected'>Off</td></tr>
</table>
</div>
<div id='nest_remote' class='remote-div' onClick='hideRemote()'>
<table border='1' class='remote-table'>
<tr><td id='nest_remote-away' colspan='2' class='nest_remote-button remote-not-selected'>On</td></tr>
<tr><td id='nest_remote-home' colspan='2' class='nest_remote-button remote-not-selected'>Off</td></tr>
</table>
</div>
<div id='cmd_div' style='visibility: hidden'>
<iframe id='cmd' src=''></iframe>
<iframe id='nest' src='nest.php' onLoad='getTemp()'></iframe>
<input type='hidden' id='remote_change'>
</div>
";
print "
<script>";
foreach ( $device as $curr_device ) { 
  print "reloadStatus('".$curr_device[id]."');";
}
print "</script>";
print "
<table align='center'>
  <tr>";
$other_services = $ini_array["other_services"];
foreach ($other_services as $service_name => $service_url) {
  print "    <td><a target='_blank' href='".$service_url."'><img class='other-service-icon' src='".$service_name.".png'></a></td>\n";
}
print "    <td><img src='away.png' class='other-service-icon' title='Set Away Mode' onClick='setAway();'></td>\n";
print "  </tr>
</table>
";
?>
