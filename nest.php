<?php
/*
I'd like to thank Scott Baker for his pynest work, it's the only reason
this code is here:
 https://github.com/smbaker/pynest
*/

session_start();
if ( $_SESSION["authenticated"] != 'true' ) {
  print "ERROR: Not authorized";
  exit();
}

$ini_array = parse_ini_file("/etc/insteon.ini", true);
$nest_info = $ini_array["nest"];

if ( $nest_info["nest_username"] == "" ) {
  print "ERROR: Nest username not defined";
  exit();
}

$post_string = 'username='.urlencode($nest_info["nest_username"]).'&password='.urlencode($nest_info["nest_password"]);
$curl_login = curl_init();
curl_setopt($curl_login, CURLOPT_USERAGENT, 'Nest/1.1.0.10 CFNetwork/548.0.4');
curl_setopt($curl_login, CURLOPT_POST, true);
curl_setopt($curl_login, CURLOPT_POSTFIELDS, $post_string);
curl_setopt($curl_login, CURLOPT_URL, 'https://home.nest.com/user/login');
curl_setopt($curl_login, CURLOPT_RETURNTRANSFER, true);
$login_resp = curl_exec($curl_login);
curl_close($curl_login);

$login_resp = json_decode($login_resp,true);
$access_token = $login_resp["access_token"];
$transport_url = $login_resp["urls"]["transport_url"];
$userid = $login_resp["userid"];

$curl_status = curl_init();
curl_setopt($curl_status, CURLOPT_URL, $transport_url.'/v2/mobile/user.'.$userid);
curl_setopt($curl_status, CURLOPT_USERAGENT, 'Nest/1.1.0.10 CFNetwork/548.0.4');
curl_setopt($curl_status, CURLOPT_HTTPHEADER, array(
    'Authorization:Basic '.$access_token,
    'X-nl-user-id: '.$userid,
    'X-nl-protocol-version: 1'
    ));
curl_setopt($curl_status, CURLOPT_RETURNTRANSFER, true);
$status_resp = curl_exec($curl_status);
curl_close($curl_status);

$status_resp = json_decode($status_resp,true);

function convert_temp($temperature, $units) {
  if ( $units == "F" ) {
    $temperature = $temperature * 1.8 + 32;
  }
  return round($temperature);
}

foreach ( $status_resp["structure"]  as $home ) {
  if ( $home["away"] == 1 ) {
    $away = 'away';
  } else {
    $away = 'home';
  }
}

foreach ($status_resp["shared"] as $serial => $thermostat) {
  $room = $nest_info[$thermostat["name"]];
  if ( $room == "" ) {
    $room = $thermostat["name"];
  }
  $units = $status_resp["device"][$serial]["temperature_scale"];  
  $curr_temp = convert_temp($thermostat["current_temperature"], $units);
  $target_temp = convert_temp($thermostat["target_temperature"], $units);
  $mode = 'off';
  if ( $thermostat["hvac_heater_state"] == 1 ) {
    $mode = 'heat';
  } elseif ( $thermostat["hvac_ac_state"] == 1 ) {
    $mode = 'cool';
  } elseif ( $thermostat["hvac_fan_state"] == 1 ) {
    $mode = 'fan';
  } else {
    $mode = 'off';
  }
  print $room.",".$curr_temp."&deg;".$units.",".$target_temp."&deg;".$units.",".$mode.",".$away."<br>";
}

#print "<pre>";
#print_r($status_resp);

?>

