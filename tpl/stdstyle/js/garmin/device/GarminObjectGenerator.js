/**
 * This snippet of code is required to generate the object tag
 * only when the browser reports that the plugin is installed.
 * 
 * @requires PluginDetect 
 */
if (PluginDetect.detectGarminCommunicatorPlugin()) {
	document.write('<object id="GarminActiveXControl" style="WIDTH: 0px; HEIGHT: 0px; visible: hidden" height="0" width="0" classid="CLSID:099B5A62-DE20-48C6-BF9E-290A9D1D8CB5">');
	document.write('	<object id="GarminNetscapePlugin" type="application/vnd-garmin.mygarmin" width="0" height="0">&#160;</object>');
	document.write('</object>');  										
}