
#APC monitor:

* apc.php script is monitor of APCu cache.

Current version of this file can be found here:
[https://github.com/krakjoe/apcu/blob/master/apc.php](https://github.com/krakjoe/apcu/blob/master/apc.php)

* apc.conf.php is file which allow integration with OC configs. 


#OC CONFIG:

DO NOT CHANGE ANYTHING IN apc.php
CHECK $config['apc'] in settingsDefault.inc.php for details about OC config for APC monitor


#UPDATE:

To update to current version just call (from within this directory):
`wget https://raw.githubusercontent.com/krakjoe/apcu/master/apc.php`

After update check the apc.conf.php integrity with current version of apc.php.

