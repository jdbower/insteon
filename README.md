# insteon
A script to control INSTEON devices via a Hub 1
https://www.ebower.com/wiki/INSTEON

You need to make sure "curl" is installed (sudo apt-get install curl).

Create a ~/.insteon.conf or /etc/insteon.conf with the following information:

insteon_ip=
insteon_port=
insteon_user=
insteon_pwd=
debug=FALSE

You may also want to create a ~/.insteon.hosts or /etc/insteon.hosts to keep track of your devices. Format is same as above:

kitchen_lights=12.34.56
bedroom_lights=23.45.67

Then just run "insteon" to get help:

Syntax: insteon device command [value]
device: The device ID or insteon.hosts name of the device
command: on, fast_on, off, fast_off, fan, status, status_fan
value: The percent you'd like to set the dimmer to (0-100) or
       low|medium|high|off for fan control.

For example, to turn on and off the kitchen_lights:
insteon kitchen_lights on
insteon kitchen_lights off

To set the dimmer in the bedroom to 50%:
insteon bedroom_lights on 50

To turn on the bedroom fan:
insteon bedroom_fan fan low

To turn off the bedroom lights without a fade:
insteon bedroom_lights fast_off
