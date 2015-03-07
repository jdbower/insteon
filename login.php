<?php

function get_userid($id_token) {
  $ch = curl_init();
  $timeout = 5;
  $url = "https://www.googleapis.com/oauth2/v1/tokeninfo?id_token=".$id_token;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  $user_data = json_decode($data);
  return $user_data;
}

$ini_array = parse_ini_file("/etc/insteon.ini", true);
$users = $ini_array["valid_users"];

$id_token = $_POST[id_token];
$user = get_userid($id_token);
if ( array_search($user->{'user_id'}, $users) == FALSE ) {
  print "
<head>
  <title>INSTEON Login Failed</title>
</head>
<body>
Sorry, your user ID (".$user->{'user_id'}.") is not authorized!<br><a href='index.php'>Click here</a> to try again.
</body>";
  exit();
}

$user_id = $user->{'user_id'};
session_start();
$_SESSION["user_id"] = $user_id;
$_SESSION["id_token"] = $_POST[id_token];
$_SESSION["authenticated"] = 'true';

header('Location: insteon.php');
?>
