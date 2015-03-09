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
$user_id = $user->{'user_id'};

if ( array_search($user_id, $users) == FALSE ) {
  print "
<head>
  <title>INSTEON Login Failed</title>
</head>
<body>
";
  if ( $user_id == null ) {
    print "No user ID detected, <a href='index.php'>click here</a> to return to the login page.<br>";
  } else {
    print "Sorry, your user ID (<a href='https://plus.google.com/$user_id'>$user_id</a>) is not authorized!<p>You may contact the server owner if you wish to get access or <a href='https://security.google.com/settings/security/permissions'>click here</a> to edit your security permissions and revoke access to this application.<br>";
  }
  print "</body>";
  exit();
}

session_start();
$_SESSION["user_id"] = $user_id;
$_SESSION["id_token"] = $_POST[id_token];
$_SESSION["authenticated"] = 'true';

header('Location: insteon.php');
?>
