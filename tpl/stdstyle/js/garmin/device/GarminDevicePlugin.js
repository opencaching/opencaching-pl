if (Garmin == undefined) var Garmin = {};
/** Copyright © 2007 Garmin Ltd. or its subsidiaries.
 *
 * Licensed under the Apache License, Version 2.0 (the 'License')
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @fileoverview GarminDevicePlugin wraps the Garmin ActiveX/Netscape plugin that should be installed on your machine inorder to talk to a Garmin Gps Device.
 * The plugin is available for download from http://www.garmin.com/plugin/installation/page/update/here
 * More information is available about this plugin from http://
 * 
 * @author Carlo Latasa carlo.latasa@garmin.com
 * @version 1.0
 */

/** This api provides a set of functions to accomplish the following tasks with a Gps Device:
 * <br>
 * <br>  1) Unlocking devices allowing them to be found and accessed.
 * <br>  2) Finding avaliable devices plugged into this machine.
 * <br>  3) Reading from the device.
 * <br>  4) Writing gpx files to the device.
 * <br>  5) Downloading data to the device.
 * <br>	 6) Geting messages, getting transfer status/progress and version information from the device.
 * <br><br>
 * Note that the GarminPluginAPIV1.xsd is referenced throughout this API. Please find more information about the GarminPluginAPIV1.xsd from http://
 *  
 * @class
 * requires Prototype
 * @param pluginElement - element that references the Garmin GPS Control Web Plugin that should be installed.
 * 
 * constructor 
 * @return a new GarminDevicePlugin
 **/
Garmin.DevicePlugin = function(pluginElement){};  //just here for jsdoc
Garmin.DevicePlugin = Class.create();
Garmin.DevicePlugin.prototype = {

    /** Constructor.
     * @private
     */
	initialize: function(pluginElement) {        
	    this.plugin = pluginElement;
	    this.unlocked = false;
	    //console.debug("DevicePlugin constructor supportsFitnessWrite="+this.supportsFitnessWrite)
	},
	
	/** Unlocks the GpsControl object to be used at the given web address.  
     * More than one set of path-key pairs my be passed in, for example:
     * ['http://myDomain.com/', 'xxx','http://www.myDomain.com/', 'yyy']
     * See documentation site for more info on getting a key.
     * 
     * @param pathKeyPairsArray - baseURL and key pairs.  
     * @type Boolean
     * @return true if successfully unlocked or undefined otherwise
     */
	unlock: function(pathKeyPairsArray) {
	    var len = pathKeyPairsArray ? pathKeyPairsArray.length / 2 : 0;
	    for(var i=0;i<len;i++) {
	    	if (this.plugin.Unlock(pathKeyPairsArray[i*2], pathKeyPairsArray[i*2+1])){
	    		this.unlocked = true;
	    		return this.unlocked;
	    	}
	    }
	    
	    // Unlock codes for local development
	    this.unlocked = this.plugin.Unlock("file:///","cb1492ae040612408d87cc53e3f7ff3c")
        	|| this.plugin.Unlock("http://localhost","45517b532362fc3149e4211ade14c9b2")
        	|| this.plugin.Unlock("http://127.0.0.1","40cd4860f7988c53b15b8491693de133");
        	
	    return this.unlocked;
	},
	
	/** Lazy-logic accessor to fitness write support var.
	 * This should NOT be called until the plug-in has been unlocked.
	 */
	getSupportsFitnessWrite: function() {
		
		if( !this.isUnlocked() ) {
			throw new Error("getSupportsFitnessWrite() must not be called before the plug-in is unlocked.");
		}
		
		// Indicates if fitness writing is supported, depending on plug-in version.
	 	this.supportsFitnessWrite = true;
	    try { 
	    	this.plugin.FinishWriteFitnessData(); //will fail if function not present
	    } catch (e) { 
	    	this.supportsFitnessWrite = false; 
	    }
	    
	    return this.supportsFitnessWrite;
	},
	
	/** Returns true if the plug-in is unlocked.
	 */
	isUnlocked: function() {
		return this.unlocked;
	},
	
	/** Initiates a find Gps devices action on the plugin. 
	 * Poll with finishFindDevices to determine when the plugin has completed this action.
	 * Use getDeviceXmlString to inspect xml contents for and array of Device nodes.
	 *   
	 */
	startFindDevices: function() {
		this.plugin.StartFindDevices();
	},

	/** Cancels the current find devices interaction.
	 */
	cancelFindDevices: function() {
        this.plugin.CancelFindDevices();
	},

	/** Poll - with this function to determine completion of 
	 * startFindGpsDevices. 
	 * 
	 * @type Boolean
	 * @return Returns true if completed finding devices otherwise false.
	 * Used after the call to startFindGpsDevices().
	 */
	finishFindDevices: function() {
    	return this.plugin.FinishFindDevices();
	},
	
	/** Returns information about the number of devices connected to this machine as 
	 * well as the names of those devices.
	 * See the GarminPluginAPIV1.xsd/ Devices/ Devices_t
	 * The xml returned should contain a 'Device' element with 'DisplayName' and 'Number'
	 * if there is a device actually conneted. 
	 *
	 * @type String
	 * @return Xml string with detailed device info
	 */
	getDevicesXml: function(){
		return this.plugin.DevicesXmlString();
	},

	/** Returns information about the specified Device indicated by the device Number. 
	 * See getDevicesXmlString to get the actual deviceNumber assigned.
	 * See the GarminPluginAPIV1.xsd/ Devices/ Devices_t
	 * 
	 * @param deviceNumber - assigned by the plugin, see getDevicesXmlString for 
	 * assignment of that number.
	 * @type String
	 * @return Xml string with detailed device info
	 */
	getDeviceDescriptionXml: function(deviceNumber){
		return this.plugin.DeviceDescription(deviceNumber);
	},
	
	/** Initiates the read from the gps device conneted. Use finishReadFromGps and getGpsProgressXml to 
	 * determine when the plugin is done with this operation. Also, use getGpsXml to extract the
	 * actual data from the device.
	 * 
	 * @param {Number} - deviceNumber assigned by the plugin, see getDevicesXmlString for 
	 * assignment of that number.
	 */
	startReadFromGps: function(deviceNumber) {
		 this.plugin.StartReadFromGps( deviceNumber );
	},

	/** This is used to indicate the status of the read process. It will return an integer
	 * know as the completion state.  The purpose is to show the 
 	 * user information about what is happening to the plugin while it 
 	 * is servicing your request. Used after startReadFromGps().
	 * 
	 * @type Number
	 * @return Completion state -  The completion state can be one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
	 **/
	finishReadFromGps: function() {
		return this.plugin.FinishReadFromGps();
	},
	
	/** Cancels the current read from the device.
     */	
	cancelReadFromGps: function() {
		this.plugin.CancelReadFromGps();
	},

	/** Initates writing the gpsXml to the device specified by deviceNumber with a filename set by filename.
	 * The gpsXml is typically in GPX fomat and the filename is only the name without the extension. The 
	 * plugin will append the .gpx extension automatically.
	 * 
	 * Use finishWriteToGps to poll when the write operation/plugin is complete.
	 * 
	 * Uses the helper functions to set the xml info and the filename.  
	 * 
	 * @param gpsXml - the gps/gpx information that should be transferred to the device.
	 * @param filename - the desired filename for the gpsXml that shall end up on the device.
	 * @param deviceNumber - the device number assigned by the plugin.  
	 */
	startWriteToGps: function(gpsXml, filename, deviceNumber) {
		this._setWriteGpsXml(gpsXml);
		this._setWriteFilename(filename);
	    this.plugin.StartWriteToGps(deviceNumber);
	},

	/** Sets the gps xml content that will end up on the device once the transfer is complete.
	 * Use in conjunction with startWriteToGps to initiate the actual write.
	 *
	 * @private 
	 * @param gpsXml - xml data that is to be written to the device. Must be in GPX format.
	 */
	_setWriteGpsXml: function(gpsXml) {
    	this.plugin.GpsXml = gpsXml;
	},

	/** This the filename that wil contain the gps xml once the transfer is complete. Use with 
	 * setWriteGpsXml to set what the file contents will be. Also, use startWriteToGps to 
	 * actually make the write happen.
	 * 
	 * @private
	 * @param filename - the actual filename that will end up on the device. Should only be the
	 * name and not the extension. The plugin will append the extension portion to the file name
	 * - typically .GPX.
	 */
	_setWriteFilename: function(filename) {
    	this.plugin.FileName = filename;
	},

	/** This is used to indicate the status of the write process. It will return an integer
	 * know as the completion state.  The purpose is to show the 
 	 * user information about what is happening to the plugin while it 
 	 * is servicing your request. 
 	 * 
	 * @type Number
	 * @return Completion state -  The completion state can be one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
 	 */
	finishWriteToGps: function() {
		//console.debug("Plugin.finishWriteToGps");
	   	return  this.plugin.FinishWriteToGps();
	},
    
	/** Cancels the current write operation to the gps device.
     */	
	cancelWriteToGps: function() {
		this.plugin.CancelWriteToGps();
	},

	// Fitness Data
	
	/** Start the asynchronous ReadFitnessData operation.
	 * 
	 * @param deviceNumber - assigned by the plugin, see getDevicesXmlString for 
	 * assignment of that number.
	 * @param dataTypeName - a Fitness DataType from the GarminDevice.xml retrieved with DeviceDescription
	 */
	startReadFitnessData: function(deviceNumber, dataTypeName) {
		 this.plugin.StartReadFitnessData( deviceNumber, dataTypeName );
	},

	/** Poll for completion of the asynchronous ReadFitnessData operation.
     *
     * If the CompletionState is eMessageWaiting, call MessageBoxXml
     * to get a description of the message box to be displayed to
     * the user, and then call RespondToMessageBox with the value of the
     * selected button to resume operation.
	 * 
	 * @type Number
	 * @return Completion state -  The completion state can be one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
	 */
	finishReadFitnessData: function() {
	 	 return  this.plugin.FinishReadFitnessData();
	},
	
	/** Cancel the asynchronous ReadFitnessData operation
     */	
	cancelReadFitnessData: function() {
		this.plugin.CancelReadFitnessData();
	},

	/** Start the asynchronous StartWriteFitnessData operation.
	 * 
	 * @param tcdXml - XML of TCD data
	 * @param deviceNumber - the device number, assigned by the plugin. See getDevicesXmlString for 
	 * assignment of that number.
	 * @param filename - the filename to write to on the device.
	 * @param dataTypeName - a Fitness DataType from the GarminDevice.xml retrieved with DeviceDescription
	 */
	startWriteFitnessData: function(tcdXml, deviceNumber, filename, dataTypeName) {	
		if( !this.getSupportsFitnessWrite() ) {
			throw new Error("Your Communicator Plug-in version (" + this.getPluginVersionString() + ") does not support writing fitness data.");
		}
		
		this._setWriteTcdXml(tcdXml);
		this._setWriteFilename(filename);
		this.plugin.StartWriteFitnessData(deviceNumber, dataTypeName);
	},
	
	/** This is used to indicate the status of the write process for fitness data. It will return an integer
	 * know as the completion state.  The purpose is to show the 
 	 * user information about what is happening to the plugin while it 
 	 * is servicing your request. 
 	 * 
	 * @type Number
	 * @return Completion state -  The completion state can be one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
	 */
	finishWriteFitnessData: function() {
	 	return  this.plugin.FinishWriteFitnessData();
	},
	
	/** Cancel the asynchronous ReadFitnessData operation
     */	
	cancelWriteFitnessData: function() {
		this.plugin.CancelWriteFitnessData();
	},
	
	/** Sets the tcd xml content that will end up on the device once the transfer is complete.
	 * Use in conjunction with startWriteFitnessData to initiate the actual write.
	 *
	 * @private 
	 * @param tcdXml - xml data that is to be written to the device. Must be in TCX format.
	 */
	_setWriteTcdXml: function(tcdXml) {
    	this.plugin.TcdXml = tcdXml;
	},

    /** Responds to a message box on the device.  
     * @param response should be an int which corresponds to a button value from this.plugin.MessageBoxXml
     */
    respondToMessageBox: function(response) {
        this.plugin.RespondToMessageBox(response);
    },

	/** Initates downloading the gpsDataString to the device specified by deviceNumber.
	 * The gpsDataString is typically in GPI fomat and the filename is only the name without the extension. The 
	 * plugin will append the .gpx extension automatically.
	 * 
	 * Use finishWriteToGps to poll when the write operation/plugin is complete.
	 * 
	 * Uses the helper functions to set the xml info and the filename.  
	 * 
	 * @param gpsDataString - the gpi information that should be transferred to the device.
	 * @param filename - the filename to write to on the device.
	 * @param deviceNumber - the device number assigned by the plugin. 
	 */
	startDownloadData: function(gpsDataString, filename, deviceNumber) {
		//console.debug("Plugin.startDownloadData gpsDataString="+gpsDataString);
		this._setWriteFilename(filename);
		this.plugin.StartDownloadData(gpsDataString, deviceNumber);
	},

	/** This is used to indicate the status of the download process. It will return an integer
	 * know as the completion state.  The purpose is to show the 
 	 * user information about what is happening to the plugin while it 
 	 * is servicing your request.
	 * 
	 * @type Number
	 * @return Completion state -  The completion state can be one of the following:
	 * 
	 *	0: idle
 	 * 	1: working
 	 * 	2: waiting
 	 * 	3: finished
	 */
	finishDownloadData: function() {
		//console.debug("Plugin.finishDownloadData");
		return this.plugin.FinishDownloadData();
	},

	/** Cancel the asynchrous Download Data operation
	 */
	cancelDownloadData: function() {
		this.plugin.CancelDownloadData();
	},

    /** Indicates success of StartDownloadData operation.
     * @type Boolean
     * @return True if the last StartDownloadData operation was successful
     */
    downloadDataSucceeded: function() {
		return this.plugin.DownloadDataSucceeded;
    },

    /** Indicates success of WriteToGps operation.
     * @type Boolean
     * @return True if the last ReadFromGps or WriteToGps operation was successful
     */
    gpsTransferSucceeded: function() {
		return this.plugin.GpsTransferSucceeded;
    },

    /** Indicates success of ReadFitnessData or WriteFitnessData operation.
     * @type Boolean
     * @return True if the last ReadFitnessData or WriteFitnessData operation succeeded
     */
    fitnessTransferSucceeded: function() {
		return this.plugin.FitnessTransferSucceeded;
    },

    /** This is the GpsXml information from the device. Typically called after a read operation.
     */
	getGpsXml: function(){
		return this.plugin.GpsXml;
	},

    /** This is the fitness data Xml information from the device. Typically called after a ReadFitnessData operation.
	 *
     * Schemas for the TrainingCenterDatabase format are available at
     * http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd
     */
	getTcdXml: function(){
		return this.plugin.TcdXml;
	},

    /**
     * @type String
     * @return The xml describing the message when the plug-in is waiting for input from the user.
     */
	getMessageBoxXml: function(){
		return this.plugin.MessageBoxXml;
	},
    
	/** Get the status/progress of the current state or transfer
     * @type String
     * @return The xml describing the current progress state of the plug-in.
     */	
	getProgressXml: function() {
		return this.plugin.ProgressXml;
	},

	/** Returns metadata information about the plugin version. 
     * @type String
     * @return The xml describing the user's version of the plug-in.
	 */
	getVersionXml: function() {
		return this.plugin.VersionXml;
	},
	
	/** Gets a string of the version number for the plugin the user has currently installed
     * @type String 
     * @return A string of the format "versionMajor.versionMinor.buildMajor.buildMinor", ex: "2.0.0.4"
     */	
	getPluginVersionString: function() {
		var versionArray = this.getPluginVersion();
	
		var versionString = versionArray[0] + "." + versionArray[1] + "." + versionArray[2] + "." + versionArray[3];
	    return versionString;
	},
	
	/** Gets the version number for the plugin the user has currently installed
     * @type Array 
     * @return An array of the format: [versionMajor, versionMinor, buildMajor, buildMinor].
     */	
	getPluginVersion: function() {
    	var versionMajor = parseInt(this._getElementValue(this.getVersionXml(), "VersionMajor"));
    	var versionMinor = parseInt(this._getElementValue(this.getVersionXml(), "VersionMinor"));
    	var buildMajor = parseInt(this._getElementValue(this.getVersionXml(), "BuildMajor"));
    	var buildMinor = parseInt(this._getElementValue(this.getVersionXml(), "BuildMinor"));

	    var versionArray = [versionMajor, versionMinor, buildMajor, buildMinor];
	    return versionArray;
	},
	
	/** Pulls value from xml given an element name or null if no tag exists with that name.
	 * @private
	 */
	_getElementValue: function(xml, tagName) {
		var start = xml.indexOf("<"+tagName+">");
		if (start == -1)
			return null;
		start += tagName.length+2;
		var end = xml.indexOf("</"+tagName+">");
		var result = xml.substring(start, end);
		return result;
	}
	
}