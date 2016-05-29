<?php
/*
I'd like to thank Scott Baker for his pynest work, it's the only reason
this code is here:
 https://github.com/smbaker/pynest

And Guillaume Boudreau, whose code I shamelessly stole instead of fixing my own.
 https://github.com/gboudreau/nest-api
*/

session_start();
if ( $_SESSION["authenticated"] != 'true' ) {
  print "ERROR: Not authorized";
  exit();
}

require_once('nest.class.php');

if ( isset($_GET["debug"]) ) {
  $debug = true;
} else {
  $debug = false;
}

$ini_array = parse_ini_file("/etc/insteon.ini", true);
$nest_info = $ini_array["nest"];

if ( $nest_info["nest_username"] == "" ) {
  print "ERROR: Nest username not defined";
  exit();
}

$nest = new Nest($nest_info["nest_username"], $nest_info["nest_password"]);

function convert_temp($temperature, $units, $base_units) {
  if ( $units != $base_units ) {
    if ( $units == "F" ) {
      $temperature = $temperature * 1.8 + 32;
    } else {
      $temperature = ($temperature - 32) / 1.8;
    }
  }
  if ( $units == "F" ) {
    $temperature = round($temperature);
  } else {
    $temperature = round($temperature,1);
  }
  return $temperature;
}

$locations = $nest->getUserLocations();


if ( $locations[0]->away == 1 ) {
  $away = 'away';
} else {
  $away = 'home';
}

foreach ($locations[0]->thermostats as $thermostat) {
  $info = $nest->getDeviceInfo($thermostat);
  if ( $debug == true ) {
    print "<pre>";
    print_r($info);
    print "</pre>";
  }
  $room = $nest_info[$info->name];
  if ( $room == "" ) {
    $room = $info->name;
  }

  $units = $info->scale;
  // Fix an issue where the first Nest scale sets the tone for the rest.
  if ( $base_units == "" ) {
    $base_units = $units;
  } 

  $mode = explode(",",$info->current_state->mode);
  $curr_temp = convert_temp($info->current_state->temperature,$units,$base_units);
  if ( $mode[1] != 'away' ) {
    $target_temp = $info->target->temperature;
  } else {
    if ( $mode[0] == 'heat' ) {
      $target_temp = $info->target->temperature[0];
    } else {
      $target_temp = $info->target->temperature[1];
    }
  }
  $target_temp = convert_temp($target_temp,$units,$base_units);
  if ( $info->current_state->ac == 1 ) {
    $cool_mode="cool-on";
  } else {
    $cool_mode="cool-off";
  }
  if ( $info->current_state->heat == 1 ) {
    $heat_mode="heat-on";
  } else {
    $heat_mode="heat-off";
  }
  if ( $info->current_state->fan == 1 ) {
    $fan_mode="fan-on";
  } else {
    $fan_mode="fan-off";
  }
  print $room.",".$curr_temp."&deg;".$units.",".$target_temp."&deg;".$units.",".$cool_mode.",".$heat_mode.",".$fan_mode.",".$away."<br>";
}

if ( isset($_GET["set_away"]) ) {
/*

This doesn't seem to be working too well, perhaps the key lies in:
https://github.com/gboudreau/nest-api/blob/master/nest.class.php
*/
  if ( $_GET["set_away"] == 'true' ) {
    $success = $nest->setAway(AWAY_MODE_ON);
  } elseif ( $_GET["set_away"] == 'false' ) {
    $success = $nest->setAway(AWAY_MODE_OFF);
  } else {
    exit('ERROR: set_away should be true or false, not '.$_GET["set_away"]);
  }
  if ( $debug == true ) {
    print "<pre>Setting away to ".$_GET["set_away"];
    var_dump($success); 
    print "</pre>";
  }
}
?>

