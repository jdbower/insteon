function reloadStatus(id) {
  console.log('Reloading '+id);
  document.getElementById(id+'_reload').style.visibility = 'hidden'; 
  document.getElementById(id+'_error').style.visibility = 'hidden'; 
  document.getElementById(id+'_power').style.visibility = 'hidden'; 
  document.getElementById(id+'_loading').style.visibility = 'visible'; 
  document.getElementById(id+'_status').src='status.php?device_id='+id;
}

function doneLoading(id) {
  document.getElementById(id+'_loading').style.visibility = 'hidden';
  document.getElementById(id+'_reload').style.visibility = 'visible';
  if ( document.getElementById(id+'_error').style.visibility != 'visible' ) {
    document.getElementById(id+'_power').style.visibility = 'visible';
  } else {
    document.getElementById(id+'_power').style.visibility = 'hidden';
  }
}

function isFan(id) {
  if ( document.getElementById(id+'_fan-level') === null ) {
    return 'false';
  } else {
    return 'true';
  }
}

function isDimmer(id) {
  if ( document.getElementById(id+'_level') === null ) {
    return 'false';
  } else {
    return 'true';
  }
}

function getStatus(id) {
  if ( document.getElementById(id+'_status').contentWindow.document.body.innerText == "" ) {
    console.log('Empty status for '+id);
    return 2;
  }
  console.log('Getting status of '+id);
  if (isFan(id) == 'true') {
    console.log(id+' is a fan');
    document.getElementById(id+'_fan-status').src='fan_status.php?device_id='+id;
  } else {
    doneLoading(id);
  }
  var status = document.getElementById(id+'_status').contentWindow.document.body.innerText;
  console.log(id+' has status of '+status);
  if ( /^ERROR/.test(status) ) {
    document.getElementById(id+'_error').style.visibility = 'visible';
    console.log('Error getting status for'+id+': '+status);
    return 1;
  }
  if ( status != 0 ) {
    document.getElementById(id+'_light-on').style.visibility = 'visible';
    if ( isDimmer(id) == 'true' ) {
      document.getElementById(id+'_level').innerHTML = '<b>'+status+'</b>';
      document.getElementById(id+'_level').style.visibility = 'visible';
    }
  } else {
    document.getElementById(id+'_light-on').style.visibility = 'hidden';
    if ( isDimmer(id) == 'true' ) {
      document.getElementById(id+'_level').style.visibility = 'hidden';
    }
  }
  return 0;
}

function getFanStatus(id) {
  if ( document.getElementById(id+'_fan-status').contentWindow.document.body.innerText == "" ) {
    console.log('Empty fan status for '+id);
    return 2;
  }
  console.log('Getting fan status of '+id);
  doneLoading(id);
  var fan_status = document.getElementById(id+'_fan-status').contentWindow.document.body.innerText;
  console.log(id+' has fan status of '+fan_status);
  if ( /^ERROR/.test(fan_status) ) {
    document.getElementById(id+'_error').style.visibility = 'visible';
    console.log('Error getting fan status: '+fan_status);
    return 1;
  }
  switch(fan_status) {
    case 'low':
      document.getElementById(id+'_fan-level').src='fan-low.png';
      break;
    case 'medium':
      document.getElementById(id+'_fan-level').src='fan-medium.png';
      break;
    case 'high':
      document.getElementById(id+'_fan-level').src='fan-high.png';
      break;
    case 'off':
      document.getElementById(id+'_icon').src = 'fan-off.png';
      document.getElementById(id+'_fan-level').style.visibility = 'hidden';
      return 0;
      break;
    default:
      document.getElementById(id+'_error').style.visibility = 'visible';
      console.log('Error setting fan status: '+fan_status);
      return 1;
  }
  document.getElementById(id+'_icon').src = 'fan-on.gif';
  document.getElementById(id+'_fan-level').style.visibility = 'visible';
}

function showRemote(id) {
  console.log("Showing remote for "+id);
  document.getElementById('remote_change').value = 'false';
  if ( isFan(id) == 'true' ) {
    div_id = 'fan_remote';
    var fan_stat = document.getElementById(id+'_fan-status').contentWindow.document.body.innerText;
    document.getElementById(div_id+'-fan-high').className = 'fan_remote-small-button remote-not-selected';
    document.getElementById(div_id+'-fan-medium').className = 'fan_remote-small-button remote-not-selected';
    document.getElementById(div_id+'-fan-low').className = 'fan-remote-small-button remote-not-selected';
    document.getElementById(div_id+'-fan-off').className = 'fan_remote-small-button remote-not-selected';
    document.getElementById(div_id+'-fan-high').name = id;
    document.getElementById(div_id+'-fan-medium').name = id;
    document.getElementById(div_id+'-fan-low').name = id;
    document.getElementById(div_id+'-fan-off').name = id;
    document.getElementById(div_id+'-fan-'+fan_stat).className = 'fan_remote-small-button remote-selected';
  } else {
    div_id = 'light_remote';
  }
  document.getElementById(div_id).name = id;
  var light_stat = document.getElementById(id+'_status').contentWindow.document.body.innerText;
  if ( light_stat > 0 ) {
    document.getElementById(div_id+'-light-on').className = div_id+'-button remote-selected';
    document.getElementById(div_id+'-light-off').className = div_id+'-button remote-not-selected';
  } else {
    document.getElementById(div_id+'-light-off').className = div_id+'-button remote-selected';
    document.getElementById(div_id+'-light-on').className = div_id+'-button remote-not-selected';
  }
  document.getElementById(div_id+'-light-on').name = id;
  document.getElementById(div_id+'-light-off').name = id;
  document.getElementById(div_id).style.visibility = 'visible';
}

function hideRemote() {
  var targ;
  if (!e) var e = window.event;
  if (e.target) targ = e.target;
  else if (e.srcElement) targ = e.srcElement;
  if (targ.nodeType == 3) // defeat Safari bug
    targ = targ.parentNode;
  if (( targ.id == "light_remote" ) || ( targ.id == "fan_remote" )) {
    console.log("Hiding remotes");
    if ( document.getElementById('remote_change').value != 'false' ) {
      reloadStatus(targ.name);
    }
    document.getElementById("fan_remote").style.visibility = 'hidden';
    document.getElementById("light_remote").style.visibility = 'hidden';
    return 0;
  }
  targ_data = targ.id.split("_");
  if ( targ_data[1] == "remote-light-on" ) {
    console.log('Turning '+targ.name+' on');
    setLight(targ.name, '100');
    document.getElementById(div_id+'-light-on').className = div_id+'-button remote-selected';
    document.getElementById(div_id+'-light-off').className = div_id+'-button remote-not-selected';
    document.getElementById('remote_change').value = 'true';
    return 0;
  }
  if ( targ_data[1] == "remote-light-off" ) {
    console.log('Turning '+targ.name+' off');
    setLight(targ.name, '0');
    document.getElementById(div_id+'-light-off').className = div_id+'-button remote-selected';
    document.getElementById(div_id+'-light-on').className = div_id+'-button remote-not-selected';
    document.getElementById('remote_change').value = 'true';
    return 0;
  }
  if ( targ_data[0] == "fan" ) {
    document.getElementById('fan_remote-fan-high').className = 'fan_remote-small-button remote-not-selected';
    document.getElementById('fan_remote-fan-medium').className = 'fan_remote-small-button remote-not-selected';
    document.getElementById('fan_remote-fan-low').className = 'fan_remote-small-button remote-not-selected';
    document.getElementById('fan_remote-fan-off').className = 'fan_remote-small-button remote-not-selected';
    document.getElementById(targ.id).className = 'fan_remote-small-button remote-selected';
    fan_speed = targ.id.split("-")[2];
    console.log('Setting '+targ.name+' to fan speed '+fan_speed);
    setFan(targ.name, fan_speed);
    document.getElementById('remote_change').value = 'true';
    return 0;
  }
}

function setLight(id, level) {
  console.log('Setting '+id+' to '+level);
  document.getElementById('cmd').src = 'set_light.php?device_id='+id+'&value='+level;
}

function setFan(id, level) {
  console.log('Setting '+id+' to fan speed '+level);
  document.getElementById('cmd').src = 'set_fan.php?device_id='+id+'&value='+level;
}

