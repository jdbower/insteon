function reloadStatus(id) {
  console.log('Reloading '+id);
  document.getElementById(id+'_reload').style.visibility = 'hidden'; 
  document.getElementById(id+'_error').style.visibility = 'hidden'; 
  document.getElementById(id+'_power').style.visibility = 'hidden'; 
  document.getElementById(id+'_loading').style.visibility = 'visible'; 
  document.getElementById(id+'_status').src='status.php?device_id='+id;
  document.getElementById('nest').src='nest.php';
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

function getTemp() {
  console.log('Getting Temperature');
  temps = document.getElementById('nest').contentWindow.document.body.innerText;
  temps = temps.split('\n');
  for ( index = 0; index < temps.length; ++index) {
    curr_temp = temps[index];
    if ( curr_temp != "" ) {
      curr_temp = curr_temp.split(',');
      html = curr_temp[1]+" ("+curr_temp[2]+")";
      if ( curr_temp[3] == "heat" ) {
        html = html.concat("<img class='temp-icon' src='hvac-heat.png'>");
      }
      if ( curr_temp[3] == "cool" ) {
        html = html.concat("<img class='temp-icon' src='hvac-cool.png'>");
      }
      if ( curr_temp[3] == "fan" ) {
        html = html.concat("<img class='temp-icon' src='hvac-fan.png'>");
      }
      if ( curr_temp[4] == "away" ) {
        html = html.concat("<img class='temp-icon' src='away.png'>");
      }
      
      document.getElementById(curr_temp[0]+'_temp').innerHTML = html;
    }
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
  if ( status == 'ERROR: Not authorized' ) {
    location.reload();
  }
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
  if ( fan_status == 'ERROR: Not authorized' ) {
    location.reload();
  }
  if ( /^ERROR/.test(fan_status) ) {
    document.getElementById(id+'_error').style.visibility = 'visible';
    console.log('Error getting fan status: '+fan_status);
    return 1;
  }
  switch(fan_status) {
    case 'low':
      document.getElementById(id+'_fan-level').src='fan-low.png';
      document.getElementById(id+'_fan-level').title='The fan is on low speed';
      break;
    case 'medium':
      document.getElementById(id+'_fan-level').src='fan-medium.png';
      document.getElementById(id+'_fan-level').title='The fan is on medium speed';
      break;
    case 'high':
      document.getElementById(id+'_fan-level').src='fan-high.png';
      document.getElementById(id+'_fan-level').title='The fan is on high speed';
      break;
    case 'off':
      document.getElementById(id+'_icon').src = 'fan-off.png';
      document.getElementById(id+'_fan-level').style.visibility = 'hidden';
      document.getElementById(id+'_fan-level').title='The fan is off';
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
    document.getElementById(div_id+'-fan-low').className = 'fan_remote-small-button remote-not-selected';
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

function setAway() {
  var device_list = document.getElementsByName('device_div');
  console.log("Setting away mode"); 
  index = 0;
  len = device_list.length;
  var needs_refresh = new Array();
  // First turn off lights in a one second loop.
  (function lightLoop (index) {          
    setTimeout(function () {
      dev_id = device_list[index - 1].id;
      if ( document.getElementById(dev_id+'_off-when-away').title == 'true' ) {
        console.log('Setting '+dev_id+' away.');
        if ( document.getElementById(dev_id+'_status').contentWindow.document.body.innerText != '0' ) {
          setLight(dev_id,'0');
          needs_refresh[dev_id] = 'true';
        }
      }
      if (--index) lightLoop(index);
    }, 1000)
  })(len);

  // Next turn off fans in a one second loop.
  (function fanLoop (index) {
    setTimeout(function () {
      dev_id = device_list[index - 1].id;
      if ( document.getElementById(dev_id+'_off-when-away').title == 'true' ) {
        if ( isFan(dev_id) == 'true' ) {
          if ( document.getElementById(dev_id+'_fan-status').contentWindow.document.body.innerText != 'off' ) {
            setFan(dev_id,'off');
            needs_refresh[dev_id] = 'true';
          }
        }
      }
      if (--index) fanLoop(index);
    }, 1000)
  })(len);

  // And now reload the devices
  (function reloadLoop (index) {
    setTimeout(function () {
      dev_id = device_list[index - 1].id;
      if ( needs_refresh[dev_id] == 'true' ) {
        reloadStatus(dev_id);
      }
      if (--index) reloadLoop(index);
    }, 1000)
  })(len);


}
