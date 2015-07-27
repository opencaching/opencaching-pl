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
 * @fileoverview Garmin.DeviceControl A high-level JavaScript API which supports listener and callback functionality.
 * 
 * @author Carlo Latasa carlo.latasa@garmin.com
 * @version 1.0
 */
/** A controller object that can retrieve and send data to a Garmin 
 * device.<br><br>
 * @class Garmin.DeviceControl
 * 
 * The controller must be unlocked before anything can be done with it.  
 * Then you'll have to find a device before you can start to read data from
 * and write data to the device.<br><br>
 * 
 * We use the observer pattern (http://en.wikipedia.org/wiki/Observer_pattern)
 * to handle the asynchronous nature of device communication.  You must register
 * your class as a listener to this Object and then implement methods that will 
 * get called on certain events.<br><br>
 * 
 * Events:<br><br>
 *     onStartFindDevices called when starting to search for devices.
 *       the object returned is {controller: this}<br><br>
 *
 *     onCancelFindDevices is called when the controller is told to cancel finding
 *         devices {controller: this}<br><br>
 *
 *     onFinishFindDevices called when the devices are found.
 *       the object returned is {controller: this}<br><br>
 *
 *     onException is called when an exception occurs in a method
 *         object passed back is {msg: exception}<br><br>
 *
 *	   onInteractionWithNoDevice is called when the device is lazy loaded, but finds no devices,
 * 			yet still attempts a read/write action {controller: this}<br><br>
 * 
 *     onStartReadFromDevice is called when the controller is about to start
 *         reading from the device {controller: this}<br><br>
 * 
 *     onFinishReadFromDevice is called when the controller is done reading 
 *         the device.  the read is either a success or failure, which is 
 *         communicated via json.  object passed back contains 
 *         {success:this.garminPlugin.GpsTransferSucceeded, controller: this} <br><br>
 *
 *     onWaitingReadFromDevice is called when the controller is waiting for input
 *         from the user about the device.  object passed back contains: 
 *         {message: this.garminPlugin.MessageBoxXml, controller: this}<br><br>
 *
 *     onProgressReadFromDevice is called when the controller is still reading information
 *         from the device.  in this case the message is a percent complete/ 
 *         {progress: this.getDeviceStatus(), controller: this}<br><br>
 *
 *     onCancelReadFromDevice is called when the controller is told to cancel reading
 *         from the device {controller: this}<br><br>
 *
 *     onFinishWriteToDevice is called when the controller is done writing to 
 *         the device.  the write is either a success or failure, which is 
 *         communicated via json.  object passed back contains 
 *         {success:this.garminPlugin.GpsTransferSucceeded, controller: this}<br><br>
 *
 *     onWaitingWriteToDevice is called when the controller is waiting for input
 *         from the user about the device.  object passed back contains: 
 *         {message: this.garminPlugin.MessageBoxXml, controller: this}<br><br>
 *
 *     onProgressWriteToDevice is called when the controller is still writing information
 *         to the device.  in this case the message is a percent complete/ 
 *         {progress: this.getDeviceStatus(), controller: this}<br><br>
 *
 *     onCancelWriteToDevice is called when the controller is told to cancel writing
 *         to the device {controller: this}<br><br>
 *
 * @constructor 
 *
 * requires Prototype
 * @requires BrowserDetect
 * @requires Garmin.DevicePlugin
 * @requires Garmin.Broadcaster
 * @requires Garmin.XmlConverter
 */
Garmin.DeviceControl = function(){}; //just here for jsdoc
Garmin.DeviceControl = Class.create();
Garmin.DeviceControl.prototype = {


	/////////////////////// Initialization Code ///////////////////////	

    /** Instantiates a Garmin.DeviceControl object, but does not unlock/activate plugin.
     */
	initialize: function() {
		
		this.pluginUnlocked = false;
		
		try {
			if (typeof(Garmin.DevicePlugin) == 'undefined') throw '';
		} catch(e) {
			throw new Error(Garmin.DeviceControl.MESSAGES.deviceControlMissing);
		};

    	// check that the browser is supported
     	if(!BrowserSupport.isBrowserSupported()) {
    	    var notSupported = new Error(Garmin.DeviceControl.MESSAGES.browserNotSupported);
    	    notSupported.name = "BrowserNotSupportedException";
    	    //console.debug("Control._validatePlugin throw BrowserNotSupportedException")
    	    throw notSupported;
        }
		
		// make sure the browser has the plugin installed
		if (!PluginDetect.detectGarminCommunicatorPlugin()) {
     	    var notInstalled = new Error(Garmin.DeviceControl.MESSAGES.pluginNotInstalled);
    	    notInstalled.name = "PluginNotInstalledException";
    	    throw notInstalled;			
		}
				
		// grab the plugin object on the page
		var pluginElement;
		if( window.ActiveXObject ) { // IE
			pluginElement = $("GarminActiveXControl");
		} else { // FireFox
			pluginElement = $("GarminNetscapePlugin");
		}
		
		// make sure the plugin object exists on the page
		if (pluginElement == null) {
			var error = new Error(Garmin.DeviceControl.MESSAGES.missingPluginTag);
			error.name = "HtmlTagNotFoundException";
			throw error;			
		}
		
		// instantiate a garmin plugin
		this.garminPlugin = new Garmin.DevicePlugin(pluginElement);
		
		// validate the garmin plugin
		this._validatePlugin();
		
		// instantiate a broacaster
		this._broadcaster = new Garmin.Broadcaster();

		this.getDetailedDeviceData = true;
		this.devices = new Array();
		this.deviceNumber = null;
		this.numDevices = 0;

		this.gpsData = null;
		this.gpsDataType = null; //used by both read and write methods to track data context
		this.gpsDataString = "";
		//this.wasMessageHack = false; //needed because garminPlugin.finishDownloadData returns true after out-of-memory error message is returned
	},

	/** Checks plugin validity: browser support, installation and version.
	 * @private
     * @throws BrowserNotSupportedException
     * @throws PluginNotInstalledException
     * @throws OutOfDatePluginException
     */
    _validatePlugin: function() {
		if (!this.isPluginInstalled()) {
     	    var notInstalled = new Error(Garmin.DeviceControl.MESSAGES.pluginNotInstalled);
    	    notInstalled.name = "PluginNotInstalledException";
    	    throw notInstalled;
        }
		if(this.isPluginOutOfDate()) {
    	    var outOfDate = new Error(Garmin.DeviceControl.MESSAGES.outOfDatePlugin1+Garmin.DeviceControl.REQUIRED_VERSION.toString()+Garmin.DeviceControl.MESSAGES.outOfDatePlugin2+this.getPluginVersionString());
    	    outOfDate.name = "OutOfDatePluginException";
    	    outOfDate.version = this.getPluginVersionString();
    	    throw outOfDate;
        }
    },


	/////////////////////// Device Handling Methods ///////////////////////	

	/** Finds any connected Garmin Devices.  
     * When it's done finding the devices, onFinishFindDevices is dispatched
     * this.numDevices = the number of devices found
     * this.deviceNumber is the device that we'll use to communicate with
     * Use this.getDevices() to get an array of the found devices and 
     * this.setDeviceNumber({Number}) to change the device
     */	
	findDevices: function() {
		if (!this.isUnlocked())
			throw new Error(Garmin.DeviceControl.MESSAGES.pluginNotUnlocked);
        this.garminPlugin.startFindDevices();
	    this._broadcaster.dispatch("onStartFindDevices", {controller: this});
        setTimeout(function() { this._finishFindDevices() }.bind(this), 1000);
	},

	/** Cancels the current find devices interaction
     */	
	cancelFindDevices: function() {
		this.garminPlugin.cancelFindDevices();
    	this._broadcaster.dispatch("onCancelFindDevices", {controller: this});
	},

	/** Loads device data into devices array.
	 * @private
     */	
	_finishFindDevices: function() {
    	if(this.garminPlugin.finishFindDevices()) {
            //console.debug("_finishFindDevices devXml="+this.garminPlugin.getDevicesXml())
            this.devices = Garmin.PluginUtils.parseDeviceXml(this.garminPlugin, this.getDetailedDeviceData);
            //console.debug("_finishFindDevices devXml="+this.garminPlugin.getDevicesXml())
            
            this.numDevices = this.devices.length;
       		this.deviceNumber = 0;
	        this._broadcaster.dispatch("onFinishFindDevices", {controller: this});
    	} else {
    		setTimeout(function() { this._finishFindDevices() }.bind(this), 500);
    	}
	},

	/** Sets the deviceNumber variable which determines which connected device to talk to.
     * @param {Number} The device number
     */	
	setDeviceNumber: function(deviceNumber) {
		this.deviceNumber = deviceNumber;
	},

	/** Get a list of the devices found
     * @type Array<Garmin.Device>
     */	
	getDevices: function() {
		return this.devices;
	},


	/////////////////////// Web Drop Methods ///////////////////////
	
    /** Writes an address to the currently selected device.
     * 
     * @param {String} address to be written to the device. This doesn't check validity
     */	
	writeAddressToDevice: function(address) {
		if (!this.isUnlocked())
			throw new Error(Garmin.DeviceControl.MESSAGES.pluginNotUnlocked);
		if (!this.geocoder) {
			this.geocoder = new Garmin.Geocode();
			this.geocoder.register(this);
		}
		this.geocoder.findLatLng(address);
	},

	/** Handles call-back from geocoder and forwards call to onException on registered listeners.
	 * @private
     * @param {Error} error wrapped in JSON 'msg' object.
     */
	onException: function(json) {
		this._reportException(json.msg);
	},
	
	/** Handles call-back from geocoder and forwards call to writeToDevice.
	 * Registered listeners will recieve an onFinishedFindLatLon call before writeToDevice is invoked.
	 * Listeners can change the 'fileName' if they choose avoiding overwritting old waypoints on
	 * some devices.
	 * @private
     * @param {Object} waypoint, fileName and controller in JSON wrapper.
     */
	onFinishedFindLatLon: function(json) {
		json.fileName = "address.gpx";
		json.controller = this;
		this._broadcaster.dispatch("onFinishedFindLatLon", json);
   		var factory = new Garmin.GpsDataFactory();
		var gpxStr = factory.produceGpxString(null, [json.waypoint]);
		this.writeToDevice(gpxStr, json.fileName);
	},


	/////////////////////// Read Methods ///////////////////////
	
	
	/** Asynchronously reads GPX data from the connected device.  Only handles reading
     * from the device in this.deviceNumber
     * 
     * When the data has been gathered, the onFinishedReadFromDevice is fired, and the
     * data is stored in this.gpsDataString and this.gpsData
     */
	readFromDevice: function() {
		this.readDataFromDevice(Garmin.DeviceControl.FILE_TYPES.gpx);
	},
	
	/** Asynchronously reads fitness history data (TCX) from the connected device.  Only handles 
     * reading from the device in this.deviceNumber
     * 
     * When the data has been gathered, the onFinishedReadFromDevice is fired, and the
     * data is stored in this.gpsDataString
     */	
	readFromDeviceFitness: function() {	
		this.readDataFromDevice(Garmin.DeviceControl.FILE_TYPES.tcx);
	},
	
//	/** Currently not in use, but written for possible future use.
//	 * 
//	 * Asynchronously reads fitness course data (CRS) from the connected device.  Only handles 
//     * reading from the device in this.deviceNumber
//     * 
//     * When the data has been gathered, the onFinishedReadFromDevice is fired, and the
//     * data is stored in this.gpsDataString
//     */	
//	readCourseFromFitnessDevice: function() {
//		this.readDataFromDevice(Garmin.DeviceControl.FILE_TYPES.crs);
//	},
	
	/** Generic read method.
	 * @param fileType - String, Possible values for fileType are located in Garmin.DeviceControl.FILE_TYPES
     */	
	readDataFromDevice: function(fileType) {
		if (!this.isUnlocked())
			throw new Error(Garmin.DeviceControl.MESSAGES.pluginNotUnlocked);
		if (this.numDevices == 0)
			throw new Error(Garmin.DeviceControl.MESSAGES.noDevicesConnected);
		if ( ! this._isAMember(fileType, [Garmin.DeviceControl.FILE_TYPES.gpx, Garmin.DeviceControl.FILE_TYPES.tcx])) {
			var error = new Error(Garmin.DeviceControl.MESSAGES.invalidFileType + fileType);
			error.name = "InvalidTypeException";
			throw error;
		}
		this.gpsDataType = fileType;
		this.gpsData = null;		
		this.gpsDataString = null;
		this.idle = false;
		try {
        	this._broadcaster.dispatch("onStartReadFromDevice", {controller: this});
        	if (this.gpsDataType == Garmin.DeviceControl.FILE_TYPES.gpx) {
		    	this.garminPlugin.startReadFromGps( this.deviceNumber );	        		
        	} else if (this.gpsDataType == Garmin.DeviceControl.FILE_TYPES.tcx) {
		    	this.garminPlugin.startReadFitnessData( this.deviceNumber, Garmin.DeviceControl.FILE_TYPES.tcx );	        		
        	}
		    this._progressRead();
		} catch(e) {
		    this._reportException(e);
		}
	},
	
	/** Internal read dispatching and polling delay.
	 * @private
     */	
	_progressRead: function() {
		this._broadcaster.dispatch("onProgressReadFromDevice", {progress: this.getDeviceStatus(), controller: this});
        setTimeout(function() { this._finishReadFromDevice() }.bind(this), 200);        		 
	},
	
	/** Internal read state logic.
	 * @private
     */	
	_finishReadFromDevice: function() {
		var isGPX = (this.gpsDataType == Garmin.DeviceControl.FILE_TYPES.gpx);
		var completionState = isGPX ? this.garminPlugin.finishReadFromGps() : this.garminPlugin.finishReadFitnessData();
		//console.debug("control._finishReadFromDevice this.gpsDataType="+this.gpsDataType+" completionState="+completionState)
        try {
			if( completionState == Garmin.DeviceControl.FINISH_STATES.finished ) {
				if(this.gpsDataType == Garmin.DeviceControl.FILE_TYPES.gpx) { 
					if (this.garminPlugin.gpsTransferSucceeded()) {
						this.gpsDataString = this.garminPlugin.getGpsXml();
						this.gpsData = Garmin.XmlConverter.toDocument(this.gpsDataString);
						this._broadcaster.dispatch("onFinishReadFromDevice", {success: this.garminPlugin.gpsTransferSucceeded(), controller: this});											
					}
				} else if (this.gpsDataType == Garmin.DeviceControl.FILE_TYPES.tcx) {
					if (this.garminPlugin.fitnessTransferSucceeded()) {
						this.gpsDataString = this.garminPlugin.getTcdXml();
						this.gpsData = Garmin.XmlConverter.toDocument(this.gpsDataString);
						this._broadcaster.dispatch("onFinishReadFromDevice", {success: this.garminPlugin.fitnessTransferSucceeded(), controller: this});										
					}
				}
			} else if( completionState == Garmin.DeviceControl.FINISH_STATES.messageWaiting ) {
				var msg = this._messageWaiting();
				this._broadcaster.dispatch("onWaitingReadFromDevice", {message: msg, controller: this});
			} else {
	    	    this._progressRead();
			}
		} catch( aException ) {
 			this._reportException( aException );
		}
    },

	
	/** User canceled the read.
     */	
	cancelReadFromDevice: function() {
		if (this.gpsDataType == Garmin.DeviceControl.FILE_TYPES.gpx) {
			this.garminPlugin.cancelReadFromGps();
		} else {
			this.garminPlugin.cancelReadFitnessData();
		}
    	this._broadcaster.dispatch("onCancelReadFromDevice", {controller: this});
	},


	/////////////////////// Write Methods ///////////////////////	


    /** Writes the given GPX XML string to the device selected in this.deviceNumber
     * @param gpxString XML to be written to the device. This doesn't check validity.
     * @param fileName to write it to.  Validity is not checked here.
     */	
	writeToDevice: function(gpxString, fileName) {
		if (!this.isUnlocked())
			throw new Error(Garmin.DeviceControl.MESSAGES.pluginNotUnlocked);
		if(this.numDevices == 0)
			throw new Error(Garmin.DeviceControl.MESSAGES.noDevicesConnected);
		this.gpsDataType = Garmin.DeviceControl.FILE_TYPES.gpx;
		//this.wasMessageHack = false;
		try {
        	this._broadcaster.dispatch("onStartWriteToDevice", {controller: this});
		    this.garminPlugin.startWriteToGps(gpxString, fileName, this.deviceNumber);
		    this._progressWrite();
	    } catch(e) {
			this._reportException(e);
	   	}
	},

	/** Writes GPI info to the currently selected device.
     *
     * @param xmlDownloadDescription xml string to be written to the device.
     * @param fileName to write it to (String).
     */	
	downloadToDevice: function(xmlDownloadDescription, filename) {
		if (!this.isUnlocked())
			throw new Error(Garmin.DeviceControl.MESSAGES.pluginNotUnlocked);
		if(this.numDevices == 0)
			throw new Error(Garmin.DeviceControl.MESSAGES.noDevicesConnected);
		//console.debug("control.downloadToDevice filename="+filename+", deviceNumber"+this.deviceNumber)		
		this.gpsDataType = Garmin.DeviceControl.FILE_TYPES.gpi;
		//this.wasMessageHack = false;
		try {
		    this.garminPlugin.startDownloadData(xmlDownloadDescription, filename, this.deviceNumber );
		    this._progressWrite();
	    } catch(e) {
			this._reportException(e);
	    }
	},
	
	/** Writes the given TCX XML (course) string to the device selected in this.deviceNumber
     * @param tcxString XML (course) string to be written to the device. This doesn't check validity.
     * @param fileName String of filename to write it to on the device.  Validity is not checked here.
     */	
	writeFitnessToDevice: function(tcxString, fileName) {
		if (!this.isUnlocked())
			throw new Error(Garmin.DeviceControl.MESSAGES.pluginNotUnlocked);
		if(this.numDevices == 0)
			throw new Error(Garmin.DeviceControl.MESSAGES.noDevicesConnected);
		this.gpsDataType = Garmin.DeviceControl.FILE_TYPES.crs;
		try {
        	this._broadcaster.dispatch("onStartWriteToDevice", {controller: this});
		    this.garminPlugin.startWriteFitnessData(tcxString, this.deviceNumber, fileName, this.gpsDataType);
		    this._progressWrite();
	    } catch(e) {
			this._reportException(e);
	   	}
	},

	
	/** Internal dispatch and polling delay.
	 * @private
     */	
	_progressWrite: function() {
		//console.debug("control._progressWrite gpsDataType="+this.gpsDataType)		
    	this._broadcaster.dispatch("onProgressWriteToDevice", {progress: this.getDeviceStatus(), controller: this});
        setTimeout(function() { this._finishWriteToDevice() }.bind(this), 200);
	},
	
	/** Internal write lifecycle handling.
	 * @private
     */	
	_finishWriteToDevice: function() {
        try {
			var completionState;
			var success;
			
			switch( this.gpsDataType ) {
				
				case Garmin.DeviceControl.FILE_TYPES.gpx : 
					completionState = this.garminPlugin.finishWriteToGps();
					success = this.garminPlugin.gpsTransferSucceeded();
					break;
				case Garmin.DeviceControl.FILE_TYPES.crs :
					completionState = this.garminPlugin.finishWriteFitnessData();
					success = this.garminPlugin.fitnessTransferSucceeded();
					break;
				case Garmin.DeviceControl.FILE_TYPES.gpi :
					completionState = this.garminPlugin.finishDownloadData();
					success = this.garminPlugin.downloadDataSucceeded();
					break;
			}
			
			//if (this.wasMessageHack)
			//	success = false;
//			console.debug("control._finishWriteToDevice isGPX="+isGPX+" completionState="+completionState+", success="+this.garminPlugin.downloadDataSucceeded())
			if( completionState == Garmin.DeviceControl.FINISH_STATES.finished ) {
				this._broadcaster.dispatch("onFinishWriteToDevice", {success: success, controller: this});											
			} else if( completionState == Garmin.DeviceControl.FINISH_STATES.messageWaiting ) {
				//this.wasMessageHack = true;
				var msg = this._messageWaiting();
				this._broadcaster.dispatch("onWaitingWriteToDevice", {message: msg, controller: this});
			} else {
	    	     this._progressWrite();
			}
		} catch( aException ) {
 			this._reportException( aException );
		}
	},

	/** Cancels the current write transfer to the device
     */	
	cancelWriteToDevice: function() {
		var isGPX = (Garmin.DeviceControl.FILE_TYPES.gpx == this.gpsDataType);
		if (isGPX) {
			this.garminPlugin.cancelWriteToGps();
		} else {
			this.garminPlugin.cancelDownloadData();
		}
		this._broadcaster.dispatch("onCancelWriteToDevice", {controller: this});
	},


	/////////////////////// Support Methods ///////////////////////	


	/** Unlocks the GpsControl object to be used at the given web adress.
     * 
     * @param {Array} pathKeyPairsArray baseURL and key pairs.  
     * @type Boolean 
     * @return True if the plug-in was unlocked successfully
     */
	unlock: function(pathKeyPairsArray) {
		this.pluginUnlocked = this.garminPlugin.unlock(pathKeyPairsArray);
		return this.pluginUnlocked;
	},

	/** Register to be an event listener.  An object that is registered will be dispatched
     * a method if they have a function with the same dispatch name.  So if you register a
     * listener with an onFinishFindDevices, and the onFinishFindDevices message is called, you'll
     * get that message.  See class comments for event types
     *
     * @param {Object} Object that will listen for events coming from this object 
     * @see {Garmin.Broadcaster}
     */	
	register: function(listener) {
        this._broadcaster.register(listener);
	},

	/** DEPRICATED - This funciton will be removed in the next release!!
	 * True if plugin has been successfully created and unlocked.
	 * @type Boolean
	 * @deprecated use isUnlocked instead
	 */
	 isActivated: function() {
	 	return this.pluginUnlocked;
	 },
	 
	/** True if plugin has been successfully created and unlocked.
	 * @type Boolean
	 */
	 isUnlocked: function() {
	 	return this.pluginUnlocked;
	 },
	 
    /** Responds to a message box on the device.
     * TODO this method only works with writes - should it work with reads?
     * @param {Number} response should be an int which corresponds to a button value from this.garminPlugin.MessageBoxXml
     */
    respondToMessageBox: function(response) {
        this.garminPlugin.respondToMessageBox(response ? 1 : 2);
        this._progressWrite();
    },

	/** Called when device generates a message.
	 * This occurs when completionState == Garmin.DeviceControl.FINISH_STATES.messageWaiting.
	 * @private
     */	
	_messageWaiting: function() {
		var messageDoc = Garmin.XmlConverter.toDocument(this.garminPlugin.getMessageBoxXml());
		//var type = messageDoc.getElementsByTagName("Icon")[0].childNodes[0].nodeValue;
		var text = messageDoc.getElementsByTagName("Text")[0].childNodes[0].nodeValue;
		
		var message = new Garmin.MessageBox("Question",text);
		
		var buttonNodes = messageDoc.getElementsByTagName("Button");
		for(var i=0; i<buttonNodes.length; i++) {
			var caption = buttonNodes[i].getAttribute("Caption");
			var value = buttonNodes[i].getAttribute("Value");
			message.addButton(caption, value);
		}
		return message;
	},

	/** Get the status/progress of the current state or transfer
     * @type Garmin.TransferProgress
     */	
	getDeviceStatus: function() {
		var aProgressXml = this.garminPlugin.getProgressXml();
		var theProgressDoc = Garmin.XmlConverter.toDocument(aProgressXml);
		
		var title = "";
		if(theProgressDoc.getElementsByTagName("Title").length > 0) {
			title = theProgressDoc.getElementsByTagName("Title")[0].childNodes[0].nodeValue;
		}
		
		var progress = new Garmin.TransferProgress(title);

		var textNodes = theProgressDoc.getElementsByTagName("Text");
		for( var i=0; i < textNodes.length; i++ ) {
			if(textNodes[i].childNodes.length > 0) {
				var text = textNodes[i].childNodes[0].nodeValue;
				if(text != "") progress.addText(text);
			}
		}
		
		var percentageNode = theProgressDoc.getElementsByTagName("ProgressBar")[0];
		if(percentageNode != undefined) {
			if(percentageNode.getAttribute("Type") == "Percentage") {
				progress.setPercentage(percentageNode.getAttribute("Value"));
			} else if (percentageNode.getAttribute("Type") == "Indefinite") {
				progress.setPercentage(100);			
			}
		}

		return progress;
	},
		
	/**
	 * @private
	 */
	_isAMember: function(element, array) {
		return array.any( function(str){ return str==element; } );
	},
	
	/** Gets the version number for the plugin the user has currently installed
     * @type Array 
     * @return An array of the format: [versionMajor, versionMinor, buildMajor, buildMinor].
     */	
	getPluginVersion: function() {
		
    	return this.garminPlugin.getPluginVersion();
	},

	/** Determines if the Garmin plugin is the required version or a newer version for this 
	 * JavaScript library.
     * @type Boolean 
     * 
     * TODO: Move this function to DevicePlugin
	 */
	isPluginOutOfDate: function() {
    	var pVersion = this._versionToNumber(this.getPluginVersion());
   		var rVersion = this._versionToNumber(Garmin.DeviceControl.REQUIRED_VERSION.toArray());
        return (pVersion < rVersion);
	},
	
	/**
	 * @deprecated
	 * @private
	 */
	_majorVersionToNumber: function(versionArray) {
		if (versionArray[0] > 99 || versionArray[1] > 99)
			throw new Error("version segment is greater than 99: "+versionArray);
		return 1000000*versionArray[0] + 10000*versionArray[1];
	},

	/**
	 * TODO: Move this function to DevicePlugin
	 * @private
	 */
	_versionToNumber: function(versionArray) {
		if (versionArray[1] > 99 || versionArray[2] > 99 || versionArray[3] > 99)
			throw new Error("version segment is greater than 99: "+versionArray);
		return 1000000*versionArray[0] + 10000*versionArray[1] + 100*versionArray[2] + versionArray[3];
	},

	/** Gets a string of the version number for the plugin the user has currently installed
     * @type String 
     * @return A string of the format "versionMajor.versionMinor.buildMajor.buildMinor", ex: "2.0.0.4"
     */	
	getPluginVersionString: function() {
		return this.garminPlugin.getPluginVersionString();
	},

	/** Determines if the plugin is initialized
     * @type Boolean
     */	
	isPluginInitialized: function() {
		return (this.garminPlugin != null);
	},

	/** Determines if the plugin is installed on the user's machine
     * @type Boolean
     */	
	isPluginInstalled: function() {
		return (this.garminPlugin.getVersionXml() != undefined);
	},

	/** Internal exception handling for asynchronous calls.
	 * @private
      */	
	_reportException: function(exception) {
		this._broadcaster.dispatch("onException", {msg: exception, controller: this});
	},
	
	/** Number of devices detected by plugin.
	 * @type Number
}    */	
	getDevicesCount: function() {
	    return this.numDevices;
	},
	
	/** String representation of instance.
	 * @type String
     */	
	toString: function() {
	    return "Garmin Javascript GPS Controller managing " + this.numDevices + " device(s)";
	}
};

/** Dedicated browser support singleton.
 */
var BrowserSupport = {
    /** Determines if the users browser is currently supported by the plugin
     * @type Boolean
     */	 
	isBrowserSupported: function() {
		//console.debug("Display.isBrowserSupported BrowserDetect.OS="+BrowserDetect.OS+", BrowserDetect.browser="+BrowserDetect.browser)
		return (BrowserDetect.OS == "Windows" && (BrowserDetect.browser == "Firefox" || BrowserDetect.browser == "Mozilla" || BrowserDetect.browser == "Explorer"));
	}
};

/** Current Version of the Garmin Communicator Plugin, and a complementary toString function to print it out with
 */
Garmin.DeviceControl.REQUIRED_VERSION = {
    versionMajor: 2,
    versionMinor: 1,
    buildMajor: 0,
    buildMinor: 1,
    
    toString: function() {
        return this.versionMajor + "." + this.versionMinor + "." + this.buildMajor + "." + this.buildMinor;
    },
    
    toArray: function() {
        return [this.versionMajor, this.versionMinor, this.buildMajor, this.buildMinor];
    }	
};

/** Constants defining possible errors messages for various errors on the page
 */
Garmin.DeviceControl.MESSAGES = {
	deviceControlMissing: "Garmin.DeviceControl depends on the Garmin.DevicePlugin framework.",
	missingPluginTag: "Plug-In HTML tag not found.",
	browserNotSupported: "Your browser is not supported to use the Garmin Communicator Plug-In.",
	pluginNotInstalled: "Garmin Communicator Plugin NOT detected.",
	outOfDatePlugin1: "Your version of the Garmin Communicator Plug-In is out of date, required: ",
	outOfDatePlugin2: ", current: ",
	pluginNotUnlocked: "Garmin Plugin has not been unlocked",
	noDevicesConnected: "No device connected, can't communicate with device.",
	invalidFileType: "Cannot process the device file type: "	
};

/** Constants defining possible states when you poll the finishActions
 */
Garmin.DeviceControl.FINISH_STATES = {
	idle: 0,
	working: 1,
	messageWaiting: 2,
	finished: 3	
};

/** Constants defining possible file types associated with read and write methods.
 */
Garmin.DeviceControl.FILE_TYPES = {
	gpx:	"gpx",
	tcx:	"FitnessHistory",
	gpi:	"gpi",
	crs:	"FitnessCourses"
};

/** Constants defining the strings used by the Device.xml to indicate 
 * transfer direction of each file type
 */
Garmin.DeviceControl.TRANSFER_DIRECTIONS = {
	read:	"OutputFromUnit",
	write:	"InputToUnit",
	both:	"InputOutput"
};

/** Encapsulates the data provided by the device for the current process' progress.
 * Use this to relay progress information to the user.
 * @class Garmin.TransferProgress
 * @constructor 
 */
Garmin.TransferProgress = Class.create();
Garmin.TransferProgress.prototype = {
	initialize: function(title) {
		this.title = title;
		this.text = new Array();
		this.percentage = null;
	},
	
	addText: function(textString) {
		this.text.push(textString);
	},

    /** Get all the text entries for the transfer
     * @type Array
     */	 
	getText: function() {
		return this.text;
	},

    /** Get the title for the transfer
     * @type String
     */	 
	getTitle: function() {
		return this.title;
	},
	
	setPercentage: function(percentage) {
		this.percentage = percentage;
	},

    /** Get the completed percentage value for the transfer
     * @type Number
     */
	getPercentage: function() {
		return this.percentage;
	},

    /** String representation of instance.
     * @type String
     */	 	
	toString: function() {
		var progressString = "";
		if(this.getTitle() != null) {
			progressString += this.getTitle();
		}
		if(this.getPercentage() != null) {
			progressString += ": " + this.getPercentage() + "%";
		}
		return progressString;
	}
};


/** Encapsulates the data to display a message box to the user when the plug-in is waiting for feedback
 * @class Garmin.MessageBox
 * @constructor 
 */
Garmin.MessageBox = Class.create();
Garmin.MessageBox.prototype = {
	initialize: function(type, text) {
		this.type = type;
		this.text = text;
		this.buttons = new Array();
	},

    /** Get the type of the message box
     * @type String
     */	 
	getType: function() {
		return this.type;
	},

    /** Get the text entry for the message box
     * @type String
     */	 
	getText: function() {
		return this.text;
	},

    /** Get the text entry for the message box
     */	 
	addButton: function(caption, value) {
		this.buttons.push({caption: caption, value: value});
	},

    /** Get the buttons for the message box
     * @type Array
     */	 
	getButtons: function() {
		return this.buttons;
	},
	
	getButtonValue: function(caption) {
		for(var i=0; i< this.buttons.length; i++) {
			if(this.buttons[i].caption == caption) {
				return this.buttons[i].value;
			}
		}
		return null;
	},

    /**
	 * @type String
     */	 
	toString: function() {
		return this.getText();
	}
};

/*
 * Dynamic include of required libraries and check for Prototype
 * Code taken from scriptaculous (http://script.aculo.us/) - thanks guys!
var GarminDeviceControl = {
	require: function(libraryName) {
		// inserting via DOM fails in Safari 2.0, so brute force approach
		document.write('<script type="text/javascript" src="'+libraryName+'"></script>');
	},

	load: function() {
		if((typeof Prototype=='undefined') || 
			(typeof Element == 'undefined') || 
			(typeof Element.Methods=='undefined') ||
			parseFloat(Prototype.Version.split(".")[0] + "." +
			Prototype.Version.split(".")[1]) < 1.5) {
			throw("GarminDeviceControl requires the Prototype JavaScript framework >= 1.5.0");
		}

		$A(document.getElementsByTagName("script"))
		.findAll(
			function(s) {
				return (s.src && s.src.match(/GarminDeviceControl\.js(\?.*)?$/))
			}
		)
		.each(
			function(s) {
				var path = s.src.replace(/GarminDeviceControl\.js(\?.*)?$/,'../../');
				var includes = s.src.match(/\?.*load=([a-z,]*)/);
				var dependencies = 'garmin/device/GarminDevicePlugin' +
									',garmin/device/GarminDevice' +
									',garmin/util/Util-XmlConverter' +
									',garmin/util/Util-Broadcaster' +
									',garmin/util/Util-DateTimeFormat' +
									',garmin/util/Util-BrowserDetect' +
									',garmin/util/Util-PluginDetect' +
									',garmin/device/GarminObjectGenerator';
			    (includes ? includes[1] : dependencies).split(',').each(
					function(include) {
						GarminDeviceControl.require(path+include+'.js') 
					}
				);
			}
		);
	}
}

GarminDeviceControl.load();
 */

