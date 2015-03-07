<head>
<script src="https://apis.google.com/js/client:platform.js" async defer></script>
<script>
function signinCallback(authResult) {
  if (authResult['status']['signed_in']) {
    // Update the app to reflect a signed in user
    // Hide the sign-in button now that the user is authorized, for example:
    document.getElementById('signinButton').setAttribute('style', 'display: none');
    console.log(authResult['id_token']);
    document.getElementById('id_token').value = authResult['id_token'];
    document.getElementById('login').submit();
  } else {
    // Update the app to reflect a signed out user
    // Possible error values:
    //   "user_signed_out" - User is signed-out
    //   "access_denied" - User denied access to your app
    //   "immediate_failed" - Could not automatically log in the user
    console.log('Sign-in state: ' + authResult['error']);
  }
}
</script>
</head>
<body align="center">
Welcome to my Insteon control page. If you're not me, don't log in. If you are me, click the button below!<p>
<?php
$ini_array = parse_ini_file("/etc/insteon.ini", true);
$client_id = $ini_array["google-client-id"]["client-id"];
?>
<span id="signinButton">
  <span
    class="g-signin"
    data-callback="signinCallback"
    data-clientid="<?php print $client_id; ?>"
    data-cookiepolicy="single_host_origin"
    data-scope="profile">
  </span>
</span>
<form id="login" method="POST" action="insteon.php">
<input type="hidden" name="id_token" id="id_token" value="none">
</form>
</body>
