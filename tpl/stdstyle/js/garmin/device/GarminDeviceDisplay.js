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
 * @fileoverview Garmin.DeviceDisplay is a high-level UI widget for talking 
 * with Garmin Devices.
 * 
 * @author Michael Bina michael.bina.at.garmin.com
 * @version 1.0
 */
/** Provides the easiest avenue for getting a working instance of the plug-in onto your page.
 * Generates the UI elements and places them on the page.
 *
 * @class Garmin.DeviceDisplay
 * @constructor 
 * @param mainElement - (String) id of the element in which to generate the contents
 * 					(can also be a reference to the dom element itself).
 * @param options - (Object) Object with options (see {@link Garmin.DeviceDisplayDefaultOptions} for descriptions of possible options).
 * 
 * requires Prototype
 * @requires Garmin.DeviceControl
 */
Garmin.DeviceDisplay = function(mainElement, options){}; //just here for jsdoc
Garmin.DeviceDisplay = Class.create();
Garmin.DeviceDisplay.prototype = {

    /** Constructor.
     * @private
     */
	initialize: function(mainElement, options) {
		if(typeof(mainElement) == "string") {
			this.mainElement = $(mainElement);
		} else {
			this.mainElement = mainElement;
		}
		this.options = null;
		this.setOptions(options);

		this.garminController = null;
		this.activities = new Array();
		this.factory = null;
        this.tracks = null;
        this.waypoints = null;

		this._generateElements();

		if (this.options.unlockOnPageLoad) {
			this.getController(true);
		}
		if (this.options.autoFindDevices) {
			this.startFindDevices();
		}
	},



    ////////////////////////// UI GENERATION METHODS ///////////////////////////
    
    /** Primary UI build method.
     * @private
     */
	_generateElements: function() {
		if (BrowserSupport.isBrowserSupported() || !this.options.hideIfBrowserNotSupported) {
			this._generateStatusElement();
			if(this.options.showFindDevicesElement) {
				this._generateFindDevicesElement();
			}
			if(this.options.showReadDataElement) {
				this._generateReadDataElement();
			}
			if(this.options.showWriteDataElement) {
				this._generateWriteDataElement();
			}
			this._generateAboutElement();
			this.resetUI();
		}
	},

    /** Resets UI widgets based on state of application.
     * @param <String> optional status message.  
     */ 
	resetUI: function() {
		//console.debug("Display.resetUI")		
	    this.hideProgressBar();
	    
		var noDevicesAvailable = this.garminController ? (this.getController().numDevices==0) : true;
		if(this.options.showFindDevicesElement) {
			if (this.findDevicesButton)
				this.findDevicesButton.disabled = false;
			if (this.deviceSelect)		
				this.deviceSelect.disabled = noDevicesAvailable;
			if (this.cancelFindDevicesButton)
				this.cancelFindDevicesButton.disabled = true;
			if (this.readDataTypesSelect) 
				this.readDataTypesSelect.disabled = false;
		}
		if(this.options.showReadDataElement) {
			if (this.readDataButton) 
				this.readDataButton.disabled = noDevicesAvailable;
			if (this.cancelReadDataButton)
				this.cancelReadDataButton.disabled = true;
		}
		if(this.options.showWriteDataElement) {
			if (this.writeDataButton)
				this.writeDataButton.disabled = noDevicesAvailable;
			if (this.cancelWriteDataButton)
				this.cancelWriteDataButton.disabled = true;
		}
	},
	

    /** Build status UI components.
     * @private
     */
	_generateStatusElement: function() {
		this.statusElement = document.createElement("div");
		Element.extend(this.statusElement);
		this.statusElement.id = this.options.statusElementId;
		this.statusElement.addClassName(this.options.elementClassName);
		this.mainElement.appendChild(this.statusElement);

		// Status text
		this.statusText = document.createElement("div");
		Element.extend(this.statusText);
		this.statusText.id = this.options.statusTextId;
		this.statusElement.appendChild(this.statusText);

		// Progress Bar
		this.progressBar = document.createElement("div");
		Element.extend(this.progressBar);
		this.progressBar.id = this.options.progressBarId;
		this.progressBarDisplay = document.createElement("div");
		Element.extend(this.progressBarDisplay);
		this.progressBarDisplay.id = this.options.progressBarDisplayId;
		var wrapper = document.createElement("div");
		Element.extend(wrapper);
		wrapper.setStyle({position: 'relative'});
		wrapper.appendChild(this.progressBarDisplay);
		this.progressBar.appendChild(wrapper);
		this.statusElement.appendChild(this.progressBar);
		Element.hide(this.progressBar);
	},
	
	_createElement: function(id, text, type, parent) {
		var elem = document.createElement(type);
		Element.extend(elem);
		if (type=="a") {
			elem.href = location;
			elem.innerHTML = text;
		} else if (type=="button"){
			elem.type = type;
			elem.value = text;
		}
		elem.id = id;
		parent.appendChild(elem);		
		return elem;
	},
	
    /** Build find device UI components.
     * @private 
     */
	_generateFindDevicesElement: function() {
		this.findDevicesElement = document.createElement("div");
		Element.extend(this.findDevicesElement);
		this.findDevicesElement.id = this.options.findDevicesElementId;
		this.findDevicesElement.addClassName(this.options.elementClassName);
		this.mainElement.appendChild(this.findDevicesElement);

		// Find devices button
		this.findDevicesButton = document.createElement( this.options.useLinks ? "a" : "input" );
		Element.extend(this.findDevicesButton);
		if (this.options.useLinks) {
			this.findDevicesButton.href = "#";
			this.findDevicesButton.innerHTML = this.options.findDevicesButtonText;
		} else {
			this.findDevicesButton.type = "button";
			this.findDevicesButton.value = this.options.findDevicesButtonText;
		}
		this.findDevicesButton.id = this.options.findDevicesButtonId;
		this.findDevicesButton.addClassName(this.options.actionButtonClassName);
		this.findDevicesElement.appendChild(this.findDevicesButton);		
        this.findDevicesButton.onclick = function() {
        	this.startFindDevices();
        }.bind(this)

		// Cancel Find devices button
		if (this.options.showCancelFindDevicesButton) {
			this.cancelFindDevicesButton = document.createElement( this.options.useLinks ? "a" : "input" );
			Element.extend(this.cancelFindDevicesButton);
			if (this.options.useLinks) {
				this.cancelFindDevicesButton.href = "#";
				this.cancelFindDevicesButton.innerHTML = this.options.cancelFindDevicesButtonText;
			} else {
				this.cancelFindDevicesButton.type = "button";
				this.cancelFindDevicesButton.value = this.options.cancelFindDevicesButtonText;
			}
			this.cancelFindDevicesButton.id = this.options.cancelFindDevicesButtonId;
			this.cancelFindDevicesButton.addClassName(this.options.actionButtonClassName);
			this.cancelFindDevicesButton.disabled = true;
	        this.cancelFindDevicesButton.onclick = function() {
	        	this.cancelFindDevices();
	        }.bind(this)
			this.findDevicesElement.appendChild(this.cancelFindDevicesButton);
		}
		
		if (!this.options.showDeviceButtonsOnLoad) {
			if (this.findDevicesButton) {
				Element.hide(this.findDevicesButton);
			}
			if (this.cancelFindDevicesButton)				
				Element.hide(this.cancelFindDevicesButton);			
		}

		// Device select drop-down list
		this.deviceSelectElement = document.createElement("span");
		Element.extend(this.deviceSelectElement);
		this.deviceSelectElement.id = this.options.deviceSelectElementId;
		this.deviceSelectElement.innerHTML = "<span id=\"" + this.options.deviceSelectLabelId + "\">" + this.options.deviceSelectLabel + "</span>";
		this.findDevicesElement.appendChild(this.deviceSelectElement);

		this.deviceSelect = document.createElement("select");
		Element.extend(this.deviceSelect);
		this.deviceSelect.id = this.options.deviceSelectId;
		this.deviceSelect.disabled = true;
		this.deviceSelectElement.appendChild(this.deviceSelect);
		
		if (!this.options.showDeviceSelectOnLoad || !this.options.showDeviceSelectOnSingle || this.options.autoSelectFirstDevice) {
			Element.hide(this.deviceSelectElement);	
		}
	},

    /** Build read data UI components.
     * @private
     */
	_generateReadDataElement: function() {
		this.readDataElement = document.createElement("div");
		Element.extend(this.readDataElement);
		this.readDataElement.id = this.options.readDataElementId;
		this.readDataElement.addClassName(this.options.elementClassName);
		this.mainElement.appendChild(this.readDataElement);

		this.readDataButton = document.createElement( this.options.useLinks ? "a" : "input" );
		Element.extend(this.readDataButton);
		if (this.options.useLinks) {
			this.readDataButton.href = "#";
			this.readDataButton.innerHTML = this.options.readDataButtonText;
		} else {
			this.readDataButton.type = "button";
			this.readDataButton.value = this.options.readDataButtonText;
		}
		this.readDataButton.id = this.options.readDataButtonId;
		this.readDataButton.addClassName(this.options.actionButtonClassName);
		this.readDataButton.disabled = true;
        this.readDataButton.onclick = function() {
        	this.readDataButton.disabled = true;
        	this.cancelReadDataButton.disabled = false;
        	this.showProgressBar();
        	if (this.options.showReadDataTypesSelect) {
        		this.readSpecificTypeFromDevice(this.readDataTypesSelect.value);
        	} else if (this.options.readDataType != null) {
        		this.readSpecificTypeFromDevice(this.options.readDataType);
        	} else {
				this.readFromDevice();
        	}
        }.bind(this)
		this.readDataElement.appendChild(this.readDataButton);

		this.cancelReadDataButton = document.createElement( this.options.useLinks ? "a" : "input" );
		Element.extend(this.cancelReadDataButton);
		if (this.options.useLinks) {
			this.cancelReadDataButton.href = "#";
			this.cancelReadDataButton.innerHTML = this.options.cancelReadDataButtonText;
		} else {
			this.cancelReadDataButton.type = "button";
			this.cancelReadDataButton.value = this.options.cancelReadDataButtonText;
		}
		this.cancelReadDataButton.id = this.options.cancelReadDataButtonId;
		this.cancelReadDataButton.addClassName(this.options.actionButtonClassName);
		this.cancelReadDataButton.disabled = true;
        this.cancelReadDataButton.onclick = function() {
        	this.resetUI();
         	this.hideProgressBar();
        	this.getController().cancelReadFromDevice();
        }.bind(this)
		this.readDataElement.appendChild(this.cancelReadDataButton);

		if(!this.options.showCancelReadDataButton) {
			Element.hide(this.cancelReadDataButton);
		}
		
		if(this.options.showReadDataTypesSelect) {
			this.readDataTypesSelect = document.createElement("select");
			Element.extend(this.readDataTypesSelect);
			this.readDataTypesSelect.id = this.options.readDataTypesSelectId;
			this.readDataTypesSelect.disabled = true;
			this.readDataElement.appendChild(this.readDataTypesSelect);

			// TODO: need a more elegant way of adding options
			this.readDataTypesSelect.options[0] = new Option(this.options.gpsData, Garmin.DeviceControl.FILE_TYPES.gpx);
			this.readDataTypesSelect.options[1] = new Option(this.options.trainingData, Garmin.DeviceControl.FILE_TYPES.tcx);			
		}

		if(this.options.showReadRoutesSelect) {
			this.readRoutesElement = document.createElement("div");
			Element.extend(this.readRoutesElement);
			this.readRoutesElement.id = this.options.readRoutesElementId;
			this.readRoutesElement.addClassName(this.options.readResultsElementClass);
			this.readRoutesElement.innerHTML = "<span id=\"" + this.options.readRoutesSelectLabelId + "\">" + this.options.readRoutesSelectLabel + "</span>";

			this.readRoutesSelect = document.createElement("select");
			Element.extend(this.readRoutesSelect);
			this.readRoutesSelect.id = this.options.readRoutesSelectId;
			this.readRoutesSelect.addClassName(this.options.readResultsSelectClass);
			this.readRoutesSelect.disabled = true;
			this.readRoutesSelect.onchange = function() {
				this.displayTrack( this._seriesFromSelect(this.readRoutesSelect) );
			}.bind(this);
			this.readRoutesElement.appendChild(this.readRoutesSelect);
			this.readDataElement.appendChild(this.readRoutesElement);
			
			if(!this.options.showReadResultsSelectOnLoad) {
				Element.hide(this.readRoutesElement);
			}
		}

		if(this.options.showReadTracksSelect) {
			this.readTracksElement = document.createElement("div");
			Element.extend(this.readTracksElement);
			this.readTracksElement.id = this.options.readTracksElementId;
			this.readTracksElement.addClassName(this.options.readResultsElementClass);
			this.readTracksElement.innerHTML = "<span id=\"" + this.options.readTracksSelectLabelId + "\">" + this.options.readTracksSelectLabel + "</span>";

			this.readTracksSelect = document.createElement("select");
			Element.extend(this.readTracksSelect);
			this.readTracksSelect.id = this.options.readTracksSelectId;
			this.readTracksSelect.addClassName(this.options.readResultsSelectClass);
			this.readTracksSelect.disabled = true;
			this.readTracksSelect.onchange = function() {
				this.displayTrack( this._seriesFromSelect(this.readTracksSelect) );
			}.bind(this);
			this.readTracksElement.appendChild(this.readTracksSelect);
			this.readDataElement.appendChild(this.readTracksElement);
			
			if(!this.options.showReadResultsSelectOnLoad) {
				Element.hide(this.readTracksElement);
			}
		}
		
		if(this.options.showReadWaypointsSelect) {
			this.readWaypointsElement = document.createElement("div");
			Element.extend(this.readWaypointsElement);
			this.readWaypointsElement.id = this.options.readWaypointsElementId;
			this.readWaypointsElement.addClassName(this.options.readResultsElementClass);
			this.readWaypointsElement.innerHTML = "<span id=\"" + this.options.readWaypointsSelectLabelId + "\">" + this.options.readWaypointsSelectLabel + "</span>";

			this.readWaypointsSelect = document.createElement("select");
			Element.extend(this.readWaypointsSelect);
			this.readWaypointsSelect.id = this.options.readWaypointsSelectId;
			this.readWaypointsSelect.addClassName(this.options.readResultsSelectClass);
			this.readWaypointsSelect.disabled = true;
			this.readWaypointsSelect.onchange = function() {
				this.displayWaypoint( this._seriesFromSelect(this.readWaypointsSelect) );
			}.bind(this);
			this.readWaypointsElement.appendChild(this.readWaypointsSelect);
			this.readDataElement.appendChild(this.readWaypointsElement);
			
			if(!this.options.showReadResultsSelectOnLoad) {
				Element.hide(this.readWaypointsElement);
			}
		}
		
		// Read Tracks Google Map
		if(this.options.showReadGoogleMap) {
			this.readGoogleMap = document.createElement("div");
			Element.extend(this.readGoogleMap);
			this.readGoogleMap.id = this.options.readGoogleMapId;
			this.readGoogleMap.addClassName(this.options.readResultsElementClass);
			this.readDataElement.appendChild(this.readGoogleMap);			
			this.readMapController = new Garmin.MapController(this.options.readGoogleMapId);
		}
		
		if(this.options.showReadDataElementOnDeviceFound) {
			Element.hide(this.readDataElement);
		}
	},

    /** Build write data UI components.
     * @private
     */
	_generateWriteDataElement: function() {
		this.writeDataElement = document.createElement("div");
		Element.extend(this.writeDataElement);
		this.writeDataElement.id = this.options.writeDataElementId;
		this.writeDataElement.addClassName(this.options.elementClassName);
		this.mainElement.appendChild(this.writeDataElement);

		if (!this.options.getWriteData && !this.options.getGpiWriteDescription)
			throw new Error("Can't write data because getWriteData() function nor getGpiWriteDescription() is defined");
		this.writeDataButton = document.createElement( this.options.useLinks ? "a" : "input" );
		Element.extend(this.writeDataButton);
		if (this.options.useLinks) {
			this.writeDataButton.href = "#";
			this.writeDataButton.innerHTML = this.options.writeDataButtonText;
		} else {
			this.writeDataButton.type = "button";
			this.writeDataButton.value = this.options.writeDataButtonText;
		}
		this.writeDataButton.id = this.options.writeDataButtonId;
		this.writeDataButton.addClassName(this.options.actionButtonClassName);
		this.writeDataButton.disabled = true;		
        this.writeDataButton.onclick = function() {
        	this.writeDataButton.disabled = true;
        	this.cancelWriteDataButton.disabled = false;
        	this.showProgressBar();
        	this.writeToDevice();
        }.bind(this);
		this.writeDataElement.appendChild(this.writeDataButton);
		
		this.cancelWriteDataButton = document.createElement( this.options.useLinks ? "a" : "input" );
		Element.extend(this.cancelWriteDataButton);
		if (this.options.useLinks) {
			this.cancelWriteDataButton.href = "#";
			this.cancelWriteDataButton.innerHTML = this.options.cancelWriteDataButtonText;
		} else {
			this.cancelWriteDataButton.type = "button";
			this.cancelWriteDataButton.value = this.options.cancelWriteDataButtonText;
		}
		this.cancelWriteDataButton.id = this.options.cancelWriteDataButtonId;
		this.cancelWriteDataButton.addClassName(this.options.actionButtonClassName);
		this.cancelWriteDataButton.disabled = false;
		this.cancelWriteDataButton.onclick = function() {
			this.resetUI();
			this.hideProgressBar();
			this.getController().cancelWriteToDevice();
		}.bind(this);
		this.writeDataElement.appendChild(this.cancelWriteDataButton);
		
		if(!this.options.showCancelWriteDataButton) {
			Element.hide(this.cancelWriteDataButton);
		}

		if(this.options.showWriteDataElementOnDeviceFound) {
			Element.hide(this.writeDataElement);
		}
	},

    /** Build write data UI components.
     * @private
     */
	_generateAboutElement: function() {
		this.aboutElement = document.createElement("div");
		Element.extend(this.aboutElement);
		this.aboutElement.id = "aboutElement";
		this.aboutElement.addClassName(this.options.elementClassName);
		this.mainElement.appendChild(this.aboutElement);

		this.copyrightText = document.createElement("span");
		this.copyrightText.innerHTML = this.options.poweredByGarmin;
		this.aboutElement.appendChild(this.copyrightText);
	},

    ////////////////////////// FIND DEVICES METHODS ////////////////////////// 
    
    /** Entry point for searching for connected devices.
     * Will attempt to unlock the plugin if necessary.
     */
	startFindDevices: function() {
		this.getController(true); //try to unlock plugin
		if(this.findDevicesButton) 
	       	this.findDevicesButton.disabled = true;
	    if (this.cancelFindDevicesButton)
    		this.cancelFindDevicesButton.disabled = !this.isUnlocked();
		if (this.isUnlocked()) {
       		this.getController().findDevices();
		}
	},

    /** Entry point for cancelling search for connected devices.
     */
	cancelFindDevices: function() {
		this.resetUI();
       	this.getController().cancelFindDevices();
	},

    /** Call-back triggered before plugin searches for connected devices.
     * @see Garmin.DeviceControl
     * @see #startFindDevices
     */
     
    onStartFindDevices: function(json) {
        this.setStatus(this.options.lookingForDevices);
    },

    /** Call-back triggered after plugin has completed its search for devices.
     * @see Garmin.DeviceControl
     * @see #startFindDevices
     */
    onFinishFindDevices: function(json) {
		this.resetUI();
        var devices = [];               
        if(json.controller.numDevices > 0) {
            devices = json.controller.getDevices();               
            
            var template = (devices.length == 1) ? this.options.foundDevice : this.options.foundDevices;
            var values = {deviceName: devices[0].getDisplayName(), deviceCount: json.controller.numDevices};
            this.setStatus( new Template(template).evaluate(values) );
 
			if(this.options.showFindDevicesElement) {
				if (this.options.showDeviceButtonsOnFound) {
					if (this.findDevicesButton)
						Element.show(this.findDevicesButton);
					if (this.cancelFindDevicesButton)
						Element.show(this.cancelFindDevicesButton);	
				} else {
					if (this.findDevicesButton)
						Element.hide(this.findDevicesButton);
					if (this.cancelFindDevicesButton)
						Element.hide(this.cancelFindDevicesButton);
				}
				if ((devices.length < 2 && !this.options.showDeviceSelectOnSingle) || this.options.autoSelectFirstDevice) {
					Element.hide(this.deviceSelectElement);
				} else {
					Element.show(this.deviceSelectElement);
				}
				
				this._listDevices(devices);
			}
			
			if(this.options.showReadDataElementOnDeviceFound) {
				Element.show(this.readDataElement);
			}
			
			if(this.options.showWriteDataElementOnDeviceFound) {
				Element.show(this.writeDataElement);
			}
			
			if (this.options.autoReadData) {
	        	this.showProgressBar();
	        	if (this.options.showReadDataTypesSelect) {
	        		this.readSpecificTypeFromDevice(this.readDataTypesSelect.value);
	        	} else if (this.options.readDataType != null) {
	        		this.readSpecificTypeFromDevice(this.options.readDataType);
	        	} else {
					this.readFromDevice();
	        	}
			}
			
			if (this.options.autoWriteData) {
	        	this.showProgressBar();
        		this.writeToDevice();
			}
        } else {
        	if ((this.options.autoReadData || this.options.autoWriteData) && !this.options.showStatusElement) {
        		alert(this.options.noDeviceDetectedStatusText);
        	}
        	
			this.setStatus(this.options.noDeviceDetectedStatusText);
			
			if(this.options.showFindDevicesElement) {
				if (this.options.showFindDevicesButton) {
					Element.show(this.findDevicesButton);
				}				
				if (this.options.showCancelFindDevicesButton) {
					Element.show(this.cancelFindDevicesButton);	
				}
				if (this.options.showDeviceSelectNoDevice && !this.options.autoSelectFirstDevice) {
					Element.show(this.deviceSelectElement);
				}
			}					
        }
        if (this.options.afterFinishFindDevices) {
    		this.options.afterFinishFindDevices(devices, this);
        }
    },
    
    /** Call-back for find device cancelled.
     */
	onCancelFindDevices: function(json) {
		this.setStatus(this.options.findCancelled);
    	this.resetUI();
    },

    /** Load device list into select UI component.
     * @private
     */
	_listDevices: function(devices) {
		this._clearHtmlSelect(this.deviceSelect);
		if(this.options.showFindDevicesElement) {
			for( var i=0; i < devices.length; i++ ) {
	           	this.deviceSelect.options[i] = new Option(devices[i].getDisplayName(),devices[i].getNumber());
	           	if(devices[i].getNumber() == this.getController().deviceNumber) {
	           		this.deviceSelect.selectedIndex = i;
	           	}
			}
			this.deviceSelect.onchange = function() {
				var device = this.getController().getDevices()[this.deviceSelect.value];
				this.setStatus(this.options.using + device.getDisplayName());
			
				this.getController().setDeviceNumber(this.deviceSelect.value);
			}.bind(this)
			this.deviceSelect.disabled = false;
		}
	},

    ////////////////////////////// READ METHODS ////////////////////////////// 
    
    /** Initiation call for reading from a device.  If a fitness device is detected reads TCX
     * otherwise reads GPX.
     * Upon completion if the afterFinishReadFromDevice method is defined
     * it will be called.  At this time you may also obtain location data using the 
     * getTracks and getWaypoints methods.
     */
	readFromDevice: function() {
		var deviceNumber = this.getController().deviceNumber;
		var device = this.getController().getDevices()[deviceNumber];
		
		if (device.supportDeviceDataTypeRead(Garmin.DeviceControl.FILE_TYPES.tcx)) {
			this.getController().readFromDeviceFitness();
		} else if (device.supportDeviceDataTypeRead(Garmin.DeviceControl.FILE_TYPES.gpx)) {
			this.getController().readFromDevice();
		} else {
			var error = new Error(Garmin.DeviceControl.MESSAGES.invalidFileType);
			error.name = "InvalidTypeException";
			this.handleException(error);
		}
	},

    /** Initiation call for reading from a device. 
     * Upon completion if the afterFinishReadFromDevice method is defined
     * it will be called.  At this time you may also obtain location data using the 
     * getTracks and getWaypoints methods.
     * @param {String} extension - the file extension of the type to read: gpx or tcx
     */
	readSpecificTypeFromDevice: function(extension) {
		var deviceNumber = this.getController().deviceNumber;
		var device = this.getController().getDevices()[deviceNumber];
		
		if (extension == Garmin.DeviceControl.FILE_TYPES.tcx) {
			this.getController().readFromDeviceFitness();
		} else if (extension == Garmin.DeviceControl.FILE_TYPES.gpx) {
			this.getController().readFromDevice();
		} else {
			var error = new Error(Garmin.DeviceControl.MESSAGES.invalidFileType + extension);
			error.name = "InvalidTypeException";			
			this.handleException(error);
		}
	},

    /** Call-back for device read progress.
     */
    onProgressReadFromDevice: function(json) {
		if(this.options.showProgressBar) {
	    	this.updateProgressBar(json.progress.getPercentage());
	    }
    	this.setStatus(json.progress);
    },
    
    /** Call-back for device read cancelled.
     * see Garmin.DeviceControl
     */
	onCancelReadFromDevice: function(json) {
    	this.setStatus(this.options.cancelReadStatusText);
    	this.resetUI();
    },

    /** Call-back for device read.
     * see Garmin.DeviceControl
     */
    onFinishReadFromDevice: function(json) {
		this.setStatus(this.options.dataReadProcessing);
	    this.resetUI();
	    this.clearMapDisplay();

    	// select the correct factory for the parsing job
    	if (json.controller.gpsDataType == Garmin.DeviceControl.FILE_TYPES.gpx) {
			this.factory = Garmin.GpxActivityFactory;
    	} else if (json.controller.gpsDataType == Garmin.DeviceControl.FILE_TYPES.tcx) {
    		this.factory = Garmin.TcxActivityFactory;
    	} else {
			var error = new Error( + json.controller.gpsDataType);
			error.name = "InvalidTypeException";
			this.handleException(error);
    	}

		// parse the data into activities if possible
		if (this.factory != null) {
			// convert the data obtained from the device into activities
			this.activities = this.factory.parseDocument(json.controller.gpsData);

    		// filter the activities
    		this._applyDataFilters();
		}
		
    	this._finishReadProcessing(json);
    },

	_applyDataFilters: function() {
		var dataFilters = this.options.dataFilters;
		if (dataFilters != null) {
			for (var i = 0; i < dataFilters.length; i++) {
				if (dataFilters[i].run != null) {
					dataFilters[i].run(this.activities, garminFilterQueue);
				}
			}
		}
	},

	_finishReadProcessing: function(json) {
		if (garminFilterQueue != null && garminFilterQueue.length > 0) {
			//console.debug("waiting for filters to finish...");
			setTimeout(function(){this._finishReadProcessing(json);}.bind(this), 500);
		} else {
			// list activities and set status to indicate how many were found
	    	if (this.activities != null) {
	    		var listed = this._listActivities(this.activities);
	    		this.setStatus( new Template(this.options.dataFound).evaluate(listed) );
	    	}
	    	
			// pass data to the user if they want it			
			if (this.options.afterFinishReadFromDevice) {
				var dataString = this.factory != null ? this.factory.produceString(this.activities) : json.controller.gpsDataString;
				var dataDoc = this.factory != null ? Garmin.XmlConverter.toDocument(dataString): json.controller.gpsData;
	    		this.options.afterFinishReadFromDevice(dataString, dataDoc, json.controller.gpsDataType, this.activities);
			}
		}
	},

	_listActivities: function(activities) {
		var numOfRoutes = 0;
		var numOfTracks = 0;
		var numOfWaypoints = 0;
		
		// clear existing entries
		this._clearHtmlSelect(this.readRoutesSelect);
		this._clearHtmlSelect(this.readTracksSelect);
    	this._clearHtmlSelect(this.readWaypointsSelect);
		
		// loop through each activity
		for (var i = 0; i < activities.length; i++) {
			var activity = activities[i];
			var series = activity.getSeries();
			// loop through each series in the activity
			for (var j = 0; j < series.length; j++) {
				var curSeries = series[j];								
				if (curSeries.getSeriesType() == Garmin.Series.TYPES.history) {
					// activity contains a series of type history, list the track
					this._listTrack(activity, curSeries, i, j);
					numOfTracks++;
				} else if (curSeries.getSeriesType() == Garmin.Series.TYPES.route) {
					// activity contains a series of type route, list the route
					this._listRoute(activity, curSeries, i, j);
					numOfRoutes++;
				} else if (curSeries.getSeriesType() == Garmin.Series.TYPES.waypoint) {
					// activity contains a series of type waypoint, list the waypoint
					this._listWaypoint(activity, curSeries, i, j);				
					numOfWaypoints++;
				}
			}
		}
		if (this.options.showReadRoutesSelect) {
			if(numOfRoutes > 0) {
				Element.show(this.readRoutesElement);
				this.readRoutesSelect.disabled = false;
				this.displayTrack( this._seriesFromSelect(this.readRoutesSelect) );
			} else {
				Element.hide(this.readRoutesElement);
				this.readRoutesSelect.disabled = true;
			}
		}
		if (this.options.showReadTracksSelect) {		
			if(numOfTracks > 0) {
				Element.show(this.readTracksElement);
				this.readTracksSelect.disabled = false;
				this.displayTrack( this._seriesFromSelect(this.readTracksSelect) );
			} else {
				Element.hide(this.readTracksElement);
				this.readTracksSelect.disabled = true;
			}
		}
		if (this.options.showReadWaypointsSelect) {		
			if(numOfWaypoints > 0) {
				Element.show(this.readWaypointsElement);
				this.readWaypointsSelect.disabled = false;
				this.displayWaypoint( this._seriesFromSelect(this.readWaypointsSelect) );
			} else {
				Element.hide(this.readWaypointsElement);
				this.readWaypointsSelect.disabled = true;			
			}
		}	
		return {routes: numOfRoutes, tracks: numOfTracks, waypoints: numOfWaypoints};
	},

    /** Load route names into select UI component.
     * @private
     */    
	_listRoute: function(activity, series, activityIndex, seriesIndex) {
		// make sure the select component exists
		if (this.readRoutesSelect) {
			var routeName = activity.getAttribute(Garmin.Activity.ATTRIBUTE_KEYS.activityName);
			this.readRoutesSelect.options[this.readRoutesSelect.length] = new Option(routeName, activityIndex + "," + seriesIndex);
		}		
	},

    /** Load track name into select UI component.
     * @private
     */    
	_listTrack: function(activity, series, activityIndex, seriesIndex) {
		// make sure the select component exists
		if (this.readTracksSelect) {
			var startDate = activity.getSummaryValue(Garmin.Activity.SUMMARY_KEYS.startTime).getValue();
			var endDate = activity.getSummaryValue(Garmin.Activity.SUMMARY_KEYS.endTime).getValue();
			var values = {date:startDate.getDateString(), duration:startDate.getDurationTo(endDate)}
			var trackName = new Template(this.options.trackListing).evaluate(values);
			this.readTracksSelect.options[this.readTracksSelect.length] = new Option(trackName, activityIndex + "," + seriesIndex);
		}
	},

    /** Load waypoint name into select UI component.
     * @private
     */
	_listWaypoint: function(activity, series, activityIndex, seriesIndex) {
		// make sure the select component exists
		if (this.readWaypointsSelect) {
			var wptName = activity.getAttribute(Garmin.Activity.ATTRIBUTE_KEYS.activityName);
			this.readWaypointsSelect.options[this.readWaypointsSelect.length] = new Option(wptName, activityIndex + "," + seriesIndex);
		}
	},

    
    /** Retreive the two index string value from the selected index.
     * Activities are stored in Select objects as strings with 2 
     * numbers: "(index of array), (index of series)", for example:  "1,1".
     * @param Select select - the Select DOM instance
     * @type Garmin.Series
     * @return a Series instance
     */
    _seriesFromSelect: function(select) {
    	var indexesStr = select.options[select.selectedIndex].value;
    	var indexes = indexesStr.split(",", 2);
    	var activity = this.activities[parseInt(indexes[0])];
    	var series = activity.getSeries()[parseInt(indexes[1])];
    	return series;
    },

    
    /** Draws a simple line on the map using the Garmin.MapController.
     * @param Garmin.Series series - that contains a track. 
     */
    displayTrack: function(series) {
		if(this.options.showReadGoogleMap) {
			this.readMapController.map.clearOverlays();
			this.readMapController.drawTrack(series);
	    }
    },

    /** Draws a point (usualy as a thumb tack) on the map using the Garmin.MapController.
     * @param Garmin.Series series - that contains the lat/lon position of the point. 
     */
    displayWaypoint: function(series) {
		if(this.options.showReadGoogleMap) {
			this.readMapController.map.clearOverlays();
	        this.readMapController.drawWaypoint(series);
		}
    },

    /** Clears overlays from map.
     */
	clearMapDisplay: function() {
		if(this.options.showReadGoogleMap) {
			this.readMapController.map.clearOverlays();
		}
	},


    ////////////////////////////// WRITE METHODS ////////////////////////////// 
    
    /** Writes any supported data to the device.
     * 
     * Requires that the option writeDataType field be set correctly to any of the following values 
     * located in Garmin.DeviceControl.FILE_TYPES:
     * 
     * 		gpx, crs, or gpi
     * 
     * @throws InvalidTypeException
     */
    writeToDevice: function() {
    	//console.debug("Display.writeToDevice writeDataType="+this.options.writeDataType)
    	
    	switch( this.options.writeDataType ) {
				
			case Garmin.DeviceControl.FILE_TYPES.gpx : 
				this.getController().writeToDevice(this.options.getWriteData(), this.options.getWriteDataFileName());
				break;
			case Garmin.DeviceControl.FILE_TYPES.crs :
				this.getController().writeFitnessToDevice(this.options.getWriteData(), this.options.getWriteDataFileName());
				break;
			case Garmin.DeviceControl.FILE_TYPES.gpi :
				var xmlDescription = Garmin.GpiUtil.buildMultipleDeviceDownloadsXML(this.options.getGpiWriteDescription());
		        this.getController().downloadToDevice(xmlDescription, this.options.getWriteDataFileName());
				break;
			default:
				var error = new Error(Garmin.DeviceControl.MESSAGES.invalidFileType + this.options.writeDataType);
				error.name = "InvalidTypeException";
				this.handleException(error);
		}
    },

	/** Call-back triggered before writing to a device.
     * @see Garmin.DeviceControl
	 */
    onStartWriteToDevice: function(json) { 
     	this.setStatus(this.options.writingToDevice);
    },

	/** Call-back triggered when write has been cancelled.
     * @see Garmin.DeviceControl
	 */
    onCancelWriteToDevice: function(json) { 
    	this.setStatus(this.options.writingCancelled);
    },

    /** Call-back when the device already has a file with this name on it.  Do we want to override?  1 is yes, 2 is no
     * @see Garmin.DeviceControl
     */ 
    onWaitingWriteToDevice: function(json) { 
        if(confirm(json.message.getText())) {
            this.setStatus(this.options.overwritingFile);
            json.controller.respondToMessageBox(true);
        } else {
            this.setStatus(this.options.notOverwritingFile);
            json.controller.respondToMessageBox(false);
        }
    },

    onProgressWriteToDevice: function(json) {
	  	this.updateProgressBar(json.progress.getPercentage());
    	this.setStatus(json.progress);
    },

    onFinishWriteToDevice: function(json) {
    	this.setStatus(this.options.writtenToDevice);
	    this.resetUI();
		if (this.options.afterFinishWriteToDevice) {
    		this.options.afterFinishWriteToDevice(json.success, this);
		}
    },
    
    
    ////////////////////////////// UTILITY METHODS ////////////////////////////// 
    
    /** Sets up the device control which handles most of the work that isn't user
     * interface related.  The controller is lazy loaded the first time it is called.
     * Early calls must specify the unlock parameter, but read and write methods should
     * not because they should follow a call to findDevice.
     * @param {Boolean} optional request to unlock the plugin if not already done.
     * @private
     */
	getController: function(unlock) {
		if (!this.garminController) {
			try {
				this.garminController = new Garmin.DeviceControl();
				this.garminController.register(this);
	        } catch (e) {
	            this.handleException(e);
	            return null;
	        }
		}
		if (unlock && !this.isUnlocked()) {
			if(this.garminController.unlock(this.options.pathKeyPairsArray)) {
	        	this.setStatus(this.options.pluginUnlocked);
			} else {
	        	this.setStatus(this.options.pluginNotUnlocked);
			}
		}
		return this.garminController;
	},

	/** Plugin unlock status
	 * @type Boolean
	 */
	isUnlocked: function() {
		return (this.garminController && this.garminController.isUnlocked());
	},
	
	/** Sets options for this display object.  Any options that are set will override
	 * the default values.
	 *
	 * @see Garmin.DeviceDisplayDefaultOptions for possible options and default values.
	 * @throws InvalidOptionException
	 * @param {Object} options - Object with options.
	 *
     * 
	 */
	setOptions: function(options) {
		for(key in options || {}) {
			if ( ! (key in Garmin.DeviceDisplayDefaultOptions) ) {
				var err = new Error(key+" is not a valid option name, see Garmin.DeviceDisplayDefaultOptions");
				err.name = "InvalidOptionException";
				throw err;
			}
		}
		this.options = Object.extend(Garmin.DeviceDisplayDefaultOptions, options || {});
	},

	/**Sets the size of the select to zero which essentially clears it from 
	 * any values.
	 * @private
	 * @param {HTMLElement} select DOM element
	 */
    _clearHtmlSelect: function(select) {
		if(select) {
			select.length = 0;
		}
    },

    /** Set status text if showStatusElement is visible.
     * @param {String} text to display.
     */
	setStatus: function(statusText) {
		if(this.options.showStatusElement) {
		    this.statusText.innerHTML = statusText;
		}
	},

    /** Makes progress bar visible.
     */
	showProgressBar: function() {
		if(this.options.showStatusElement && this.options.showProgressBar) {
		    Element.show(this.progressBar);
		}
	},

    /** Hides progress bar.
     */
	hideProgressBar: function() {
		if(this.options.showStatusElement && this.options.showProgressBar) {
		    Element.hide(this.progressBar);
		}
	},

    /** Update percentage representation of progress bar.
     * @param {Number} percentage completion: 0-100 
     */
	updateProgressBar: function(value) {
		if(this.options.showStatusElement && this.options.showProgressBar && value) {
			var percent = (value <= 100) ? value : 100;
		    this.progressBarDisplay.style.width = percent + "%";
		}
	},
	
    /** Call-back for asynchronous method exceptions.
     * see Garmin.DeviceControl
     */
	onException: function(json) {
		this.handleException(json.msg);
	},
	
    /** Central exception dispatch method, default is to 
     * If customExceptionHandler is defined, all error handling is delegated to
     * that method - good luck.
     * 
     * @param {Error} error to process.
     */	
	handleException: function(error) {
		if (this.options.customExceptionHandler) {
			this.options.customExceptionHandler(error);
		} else {
    	    //console.debug("Display.handleException error="+error)
			var errorStatus;
			var hideFromBrowser = false;
			if(error.name == "BrowserNotSupportedException") {
				errorStatus = error.message;
				if (this.options.hideIfBrowserNotSupported) {
					hideFromBrowser = true;
				}
			} else if (error.name == "PluginNotInstalledException" || error.name == "OutOfDatePluginException") {
				errorStatus = error.message;
				errorStatus += " <a href=\""+Garmin.DeviceDisplay.LINKS.pluginDownload+"\" target=\"_blank\">"  + this.options.downloadAndInstall + "</a>";
			} else if (Garmin.PluginUtils.isDeviceErrorXml(error)) {
				errorStatus = Garmin.PluginUtils.getDeviceErrorMessage(error);	
			} else {
				errorStatus = error.name + ": " + error.message;	
			}						

 			this.setStatus(errorStatus);
			this.resetUI();
			//if no status UI div is defined, make sure the user sees the error
			if (!this.options.showStatusElement && !hideFromBrowser) {
				if (error.name == "PluginNotInstalledException" || error.name == "OutOfDatePluginException") {
					if (window.confirm(error.message+"\n" + this.options.installNow)) {
						window.open(Garmin.DeviceDisplay.LINKS.pluginDownload, "_blank");
					}
				} else {
					alert(errorStatus);
				}
			}
		}
	}
    
    
};

/** Constant defining links referenced in the DeviceDisplay
 */
Garmin.DeviceDisplay.LINKS = {
	pluginDownload:	"http://www.garmin.com/products/communicator/"
};

/** A queue of filters to be applied to activities after data is
 * obtained from the device.  Also used by display to determine
 * if the filtering process is finished; 
 */
var garminFilterQueue = new Array();

/** The default display options for the generated plug-in elements including
 * booleans for which sub-items to show.  Override specific option values by 
 * calling setOptions(optionsHash) on your instance of Garmin.DeviceDisplay
 * to customize your display options.
 *
 * @class 
 * @constructor 
 */
Garmin.DeviceDisplayDefaultOptions = function(){};
Garmin.DeviceDisplayDefaultOptions = {


	// ================== Plugin unlock ======================

	/**Unlock plugin when user lands on containing page which may result in security or
	 * plugin-not-installed messages.  Set to false to supress plugin acivity
	 * until user initiates an action.
	 * 
	 * @type Boolean
	 */
	unlockOnPageLoad:			true,

	/**The array of strings that contain the unlock codes for the plugin.
	* [URL1,KEY1,URL2,KEY2] add as many url/key pairs as you'd like.
	* @type String[]
	*/
	pathKeyPairsArray:			["file:///C:/dev/", "bd04dc1f5e97a6ff1ea76c564d133b7e"],


	// ================== Global Options ======================
	/**The class name used by various parts of the display to make
	 * CSS styling easier.
	 * 
	 * @see #statusElementId
	 * @see #readDataElementId
	 * @see #findDevicesElementId
	 */
	elementClassName:			"pluginElement",

	/**Display link instead of buttons.  Currently this only applies to the 'Find Devices' button.
	 * @type Boolean
	 */
	useLinks:					false,

	/**Action to take if browser is not supported:
	 * if true on't display the application,
	 * else if status bar is visible, display message, otherwise popup an alert dialog
	 * 
	 * @type Boolean
	 */
	hideIfBrowserNotSupported:		false,

	/**The function called when an error occurs.  This is here to allow
	 * custom error handling logic.  
	 * 
	 * The function should accept an arguement of type Error (Javascript 
	 * Error Object).
	 * 
	 * Error.name - the type of the error in a String format.
	 * Error.message - the detailed message of the error.
	 *
	 * Some Errors:
	 * 	PluginNotInstalledException - the plugin is not installed
	 *  OutOfDatePluginException - the plugin is out of date
	 *  BrowserNotSupportedException - the browser is not support by the site
	 * 
	 * @type Function
	 */				
	customExceptionHandler:		null, //function(error){ alert(error.name + ": " + error.message); }

	/**Class name to add for all buttons/links that perform an action
	 * @type String
	 */
	actionButtonClassName:		"actionButton",

	// ================== Status Element Options ======================
	/**The choice to display the feedback regarding the communications 
	 * with the device.
	 * 
	 * @type Boolean
	 * 
	 */
	showStatusElement:			true,
	
	/**The id of the HTML element where the statusBox is to be rendered.
	 * @type String
	 */
	statusElementId:			"statusBox",
	
	/**The id of the HTML element where the status text messages are to be displayed.
	 * @type String
	 */
	statusTextId:				"statusText",
	
	/**The progress bar is a graphical percentage bar used to display 
	 * the amount of reading/writing is complete.
	 * @type Boolean
	 */
	showProgressBar:			true,
	
	/**The container for the progress bar.
	 * @type String
	 */
	progressBarId:				"progressBar",
	
	/**The id of the element where the progress bar is to be displayed.
	 * @type String
	 */
	progressBarDisplayId:		"progressBarDisplay",


	//===================  Find Devices Element Options ===============
	
	/**Choice to display the find devices area that will search for connected devices.
	 * @type Boolean
	 */
	showFindDevicesElement:		true,
	
    /**Looks for devices as soon as the page is loaded and the plugin unlocked.
     * This might be particularly annoying in many situations since the plugin 
     * requires the user to authorize access to device information via a 
     * dialog box.
     * 
	 * @type Boolean
     */
	autoFindDevices:			false,
	
	/**<p>Controls the view of the buttons related to find devices (find & cancel) if 
	 * based on if the plugin finds one or more devices.  
	 * When set to <b>false</b> and  
	 * used with {@link #showDeviceButtonsOnLoad}=false 
	 * and {@link #autoFindDevices}=true these buttons will only
	 * show up if a device is not found (minimizing confusion for the user).
	 * </p>
	 * <p>
	 * More granular control is provided on each of the device buttons 
	 * {@link #showFindDevicesButton} and {@link #showCancelFindDevicesButton}.
	 * </p>
	 * @see #showFindDevicesElement
	 * @type Boolean
	 */
	showDeviceButtonsOnFound:	true,
	
	/**If true the buttons will show when the page is rendered. 
	 * If false, the buttons will not be displayed until the plugin detects that a device is not found. 
	 * If you choose not to see the buttons at all (regardless if device is found or not) then 
	 * {@link #showFindDevicesElement} should be set to false.
	 * 
	 * @see #showDeviceButtonsOnFound
	 * @see #showFindDevicesElement
	 * @type BSoolean
	 */
	showDeviceButtonsOnLoad:	true,
	
	/**Allows granular control to hide the find devices button independent
	 * of the {@link #showCancelFindDevicesButton} cancel button contol.
	 * @type Boolean
	 * 
	 */
	showFindDevicesButton:		true,
	
	/**The id referencing the HTML container around the find devices buttons.
	 * This is useful for CSS customizations. 
	 * <p>
	 * default = deviceBox
	 * </p>
	 * @type String
	 * 
	 */
	findDevicesElementId:		"deviceBox",
	
	/**The id referencing the find devices button.  This is useful for
	 * CSS customizations.
	 * 
	 * default = findDevicesButton
	 * @type String
	 */
	findDevicesButtonId:		"findDevicesButton",
	
	/**The text for the find device button.
	 * 
	 * @type String
	 */		
	findDevicesButtonText:			"Find Devices",	
	
	/**Controls the view of the cancel find devices button. When
	 * set to <b>false</b> the button will never show.  When
	 * set to <b>true</b> the button's behavior will depend on other
	 * settings such as {@link #showFindDevicesButton}, 
	 * {@link #showDeviceButtonsOnFound}, {@link #showDeviceButtonsOnLoad},
	 * and {@link #showFindDevicesElement}.
	 * 
	 * default= false
	 * @type Boolean
	 */	
	showCancelFindDevicesButton:		false,
	
	/**The id referencing the cancel find devices button.  This is useful for
	 * CSS customizations.
	 * 
	 * default = cancelFindDevicesButton
	 * @type String
	 */	
	cancelFindDevicesButtonId:	"cancelFindDevicesButton",
	
	/**The text for the cancel find device button.
	 * 
	 * @type String
	 */		
	cancelFindDevicesButtonText:		"Cancel Find Devices",

	/**Controls the view of the device select box.
	 * When set to <b>true</b> the select device box will show even when only
	 * one device is found.
	 * When set to <b>false</b> the select device box will hide when only
	 * one device is found.
	 * When {@link #showFindDevicesElement} is set to false, the device select
	 * box will never show.
	 * 
	 * default = false
	 * 
	 * @see #showDeviceSelectNoDevice
	 * @see #showDeviceSelectOnLoad	  	 
	 * @see #showFindDevicesElement	 
	 * @type Boolean
	 */
	showDeviceSelectOnSingle:	false,
	
	/**Controls the view of the device select box.
	 * When set to <b>true</b> the select device box will show even when
	 * no device is found.
	 * When set to <b>false</b> the select device box will hide when
	 * no device is found.
	 * When {@link #showFindDevicesElement} is set to false, the device select
	 * box will never show.
	 * 
	 * default = true
	 * 
	 * @see #showDeviceSelectOnSingle
	 * @see #showDeviceSelectOnLoad	  
	 * @see #showFindDevicesElement	 
	 * @type Boolean
	 */	
	showDeviceSelectNoDevice:	false,
	
	/**Controls the view of the device select box.
	 * When set to <b>true</b> the select device box will show when
	 * the display loads.
	 * When set to <b>false</b> the select device box will never be visible.
	 * When {@link #showFindDevicesElement} is set to false, the device select
	 * box will never show.
	 * 
	 * default = true
	 * 
	 * @see #showDeviceSelectOnSingle
	 * @see #showDeviceSelectNoDevice	  
	 * @see #showFindDevicesElement	 
	 * @type Boolean
	 */		
	showDeviceSelectOnLoad:		true,
	
	/**When more than one device is detected automaticly pick the first device.
	 * This allows single button interfaces to avoid having to ask the user to 
	 * choose the device and keeps the deviceSelect hidden.
	 * 
	 * default = false
	 * 
	 * @see #showDeviceSelectOnSingle
	 * @see #showDeviceSelectNoDevice	  
	 * @see #showFindDevicesElement	 
	 * @type Boolean
	 */		
	autoSelectFirstDevice:		false,
	
	/**The id referencing the device select box.  This is useful for
	 * CSS customizations.
	 * 
	 * default = deviceSelectBox
	 * @type String
	 */		
	deviceSelectElementId:		"deviceSelectBox",
	
	/**The label for the device select box.  Shows up next to the
	 * device select box.
	 * 
	 * @type String
	 */		
	deviceSelectLabel:			"Devices: ",	

	/**The id referencing the device select box label.  This is useful for
	 * CSS customizations.
	 * 
	 * default = deviceSelectLabel
	 * @type String
	 */			
	deviceSelectLabelId:		"deviceSelectLabel",	
	
	/**The id referencing the device select dropdown.  This is useful for
	 * CSS customizations.
	 * 
	 * default = deviceSelect
	 * @type String
	 */			
	deviceSelectId:				"deviceSelect",
	
	/**The status text that is displayed when no devices are found.
	 * 
	 * @type String
	 */			
	noDeviceDetectedStatusText:	"No devices found.",

    /**The function called when device search completes successfully or unsuccessfully.
     * The function should have two arguments:
     *  devices {Array<Garmin.Device>} - an array of device descriptors or an empty array in none were found.
     *  display {Garmin.DeviceDisplay} - the current instance of the DeveiceDisplay
	 * 
	 * @type Function
	 */				
	afterFinishFindDevices:	null, //function(devices, display){},


	// ================== Read Element Options ======================
	/**Start reading data from the device when one or more device(s)
	 * is found.
	 * 
	 * default = false
	 * 
	 * @see #autoFindDevices
	 * @see #autoWriteData	  
	 * @type Boolean
	 */					
	autoReadData:				false,
	
	/**Display the user interface associated with reading from
	 * a connected device.
	 * 
	 * default = true
	 * @type Boolean
	 */
	showReadDataElement:		true,
	
	/**Controls the view of the read data element. When
	 * set to <b>true</b> the element will only show after a
	 * device has been found.  When set to <b>false</> the
	 * element will show on page load.
	 * Behavior will depend on other settings such as
	 * and {@link #showReadDataElement}.
	 * 
	 * default= false
	 * @type Boolean
	 */
	showReadDataElementOnDeviceFound:		false,
	
	/**The id referencing the box containing read elements.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readBox
	 * @type String
	 */		
	readDataElementId:			"readBox",
	
	/**The id referencing the read data button.  This is useful for
	 * CSS customizations.
	 * 
	 * default = readDataButton
	 * @type String
	 */			
	readDataButtonId:			"readDataButton",
	
	/**The text on the read button.
	 * 
	 * @type String
	 */		
	readDataButtonText:			"Get Data",
	
	/**Controls the view of the cancel read data button. When
	 * set to <b>false</b> the button will never show.  When
	 * set to <b>true</> the button's behavior will depend on other
	 * settings such as {@link #showReadDataButton}, 
	 * and {@link #showReadDataElement}.
	 * 
	 * default= true
	 * @type Boolean
	 */	
	showCancelReadDataButton:		true,
	
	/**The id referencing the cancel read data button.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = cancelReadDataButton
	 * @type String
	 */		
	cancelReadDataButtonId:		"cancelReadDataButton",
	
	/**The text on the cancel read button.
	 * 
	 * @type String
	 */		
	cancelReadDataButtonText:	"Cancel Get Data",
	
	/**The status text that is displayed when user cancels the
	 * read progress.
	 * 
	 * @type String
	 */		
	cancelReadStatusText:		"Read cancelled",
	
	/**Controls the view of the device select box.
	 * When set to <b>true</b> the select device box will show when
	 * the display loads.
	 * When set to <b>false</b> the select device box will hide when
	 * the display loads.
	 * When {@link #showReadDataElement} is set to false, the results select
	 * box will never show.
	 * 
	 * default = false
	 *  
	 * @see #showReadDataElement	 
	 * @type Boolean
	 */		
	showReadResultsSelectOnLoad:	false,

	/**The class to set for select lists that are displaying results
	 * from a read operation.  This is useful for CSS customizations.
	 * 
	 * default = readResultsSelect
	 * @type String
	 */		
	readResultsSelectClass:			"readResultsSelect",
	
	/**The class to set for results elements.  This is useful for CSS customizations.
	 * 
	 * default = readResultsElement
	 * @type String
	 */		
	readResultsElementClass:		"readResultsElement",

	/**Display the route select dropdown.  When
	 * <@link #showReadDataElement> is set to false, the select
	 * track dropdown will not show.
	 * 
	 * default = true
	 * @see #showReadDataElement
	 * @type Boolean
	 */
	showReadRoutesSelect:		true,

	/**The id referencing the read routes element.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readRoutesElement
	 * @type String
	 */		
	readRoutesElementId	:		"readRoutesElement",
		
	/**The id referencing the route select dropdown.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readRoutesSelect
	 * @type String
	 */		
	readRoutesSelectId:			"readRoutesSelect",
	
	/**The label for the read routes select box.  Shows up next to the
	 * read routes select box.
	 * 
	 * @type String
	 */		
	readRoutesSelectLabel:		"Routes: ",	
		
	/**The id referencing the read routes select box label.  This is useful for
	 * CSS customizations.
	 * 
	 * default = readRoutesSelectLabel
	 * @type String
	 */			
	readRoutesSelectLabelId:	"readRoutesSelectLabel",		
		
	/**Display the track select dropdown.  When
	 * <@link #showReadDataElement> is set to false, the select
	 * track dropdown will not show.
	 * 
	 * default = true
	 * @see #showReadDataElement
	 * @type Boolean
	 */
	showReadTracksSelect:		true,		
		
	/**The id referencing the read tracks element.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readTracksElement
	 * @type String
	 */		
	readTracksElementId:		"readTracksElement",
	
	/**The id referencing the track select dropdown.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readTracksSelect
	 * @type String
	 */		
	readTracksSelectId:			"readTracksSelect",

	/**The label for the read tracks select box.  Shows up next to the
	 * read tracks select box.
	 * 
	 * @type String
	 */		
	readTracksSelectLabel:		"Tracks: ",

	/**The id referencing the read tracks select box label.  This is useful for
	 * CSS customizations.
	 * 
	 * default = deviceSelectLabel
	 * @type String
	 */			
	readTracksSelectLabelId:	"readTracksSelectLabel",

	/**The id referencing the read tracks element.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readTracksElement
	 * @type String
	 */		
	readWaypointsElementId:		"readWaypointsElement",

	/**Display the waypoint select dropdown.  When
	 * <@link #showReadDataElement> is set to false, the select
	 * waypoint dropdown will not show.
	 * 
	 * default = true
	 * @see #showReadDataElement
	 * @type Boolean
	 */	
	showReadWaypointsSelect:	true,
	
	/**The id referencing the waypoint select dropdown.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readWaypointsSelect
	 * @type String
	 */		
	readWaypointsSelectId:		"readWaypointsSelect",
	
	/**The label for the read waypoints select box.  Shows up next to the
	 * read tracks select box.
	 * 
	 * @type String
	 */		
	readWaypointsSelectLabel:	"Waypoints: ",

	/**The id referencing the read waypoints select box label.  This is useful for
	 * CSS customizations.
	 * 
	 * default = deviceSelectLabel
	 * @type String
	 */			
	readWaypointsSelectLabelId:	"readWaypointsSelectLabel",
	
	/**Display google map.  When <@link #showReadDataElement> is 
	 * set to false, google map will not show.
	 * 
	 * default = false
	 * @see #showReadDataElement
	 * @type Boolean
	 */		
	showReadGoogleMap:			false,
	
	/**The id referencing the google map display.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readMap
	 * @type String
	 */		
	readGoogleMapId:			"readMap",
	
	/**Tells the plug-in what data type to read from the device.  Options for this
	 * are currently constants listed in {@link Garmin.DeviceControl.FILE_TYPES}, 
	 * and the values are: crs, gpx, gpi, or null to skip this option altogether and get the default data type from 
	 * the device.
	 * <p>
	 * This property works in conjunction with the following functions, based on the datatype:
	 * <p>
	 * For CRS and GPX:	Define the getWriteData() and getWriteDataFileName() functions in your options section.
	 * <p>
	 * For GPI: Define the getWriteData() and getWriteDataFileName() functions in your options section.
	 * 			The getGpiWriteDescription() function replaces getWriteData().
	 * <p>
	 * default value = "gpx"
	 * @see #showReadDataElement
	 * @see Garmin.DeviceControl.FILE_TYPES
	 * @type String
	 */		
	readDataType:	"gpx",
	
	/**Display the dropdown select box for selecting what type
	 * of data to read from the device.  When 
	 * <@link #showReadDataElement> is set to false, 
	 * this device type select will not show.
	 * 
	 * default = false
	 * @see #showReadDataElement
	 * @type Boolean
	 */		
	showReadDataTypesSelect:	false,
	
	/**The id referencing the data type select.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = readDataTypesSelect
	 * @type String
	 */			
	readDataTypesSelectId:		"readDataTypesSelect",
	
	/**The function called when data is successfully read from
	 * the device.  The function should have three arguements:
	 * 
	 * 	dataString - the xml received from the device in String format
	 *  dataDoc - the xml received from the device in Document format
	 *  extension - the file type extension of the data, used to determine
	 * 				the type of data received.
	 *  activities - list of <@link Garmin.Activity> parsed from the xml.
	 * 
	 * @see Garmin.Activity
	 * @type Function
	 */				
	afterFinishReadFromDevice:	null, //function(dataString, dataDoc, extension, activities){},

	/**Load tracks even if they don't have a timestamp (technically these are
	 * routes).  Set to false if you need to do synchronization with existing
	 * track log database (like MotionBased does).
	 * 
	 * default = true
	 * @see #_listTracks
	 * @type Boolean
	 */		
	loadTracksWithoutATimestamp:	true,
	

	// ================== Write Element Options ======================
	/**Start writing data to the device when one or more device(s)
	 * is found.
	 * 
	 * default = false
	 * 
	 * @see #autoFindDevices
	 * @see #autoReadData
	 * @type Boolean
	 */					
	autoWriteData:				false,
	
	/**Display the user interface associated with writing to
	 * a connected device.
	 * 
	 * default = false
	 * @type Boolean
	 */	
	showWriteDataElement:		false,

	/**Controls the view of the write data element. When
	 * set to <b>true</b> the element will only show after a
	 * device has been found.  When set to <b>false</> the
	 * element will show on page load.
	 * Behavior will depend on other settings such as
	 * and {@link #showWriteDataElement}.
	 * 
	 * default= false
	 * @type Boolean
	 */
	showWriteDataElementOnDeviceFound:		false,
	
	/**The id referencing the write data button.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = writeDataButton
	 * @type String
	 */		
	writeDataButtonId:			"writeDataButton",
	
	/**The text on the write button.
	 * 
	 * @type String
	 */		
	writeDataButtonText:		"Write",
	
	/**Controls the view of the cancel write data button. When
	 * set to <b>false</b> the button will never show.  When
	 * set to <b>true</> the button's behavior will depend on other
	 * settings such as {@link #showWriteDataButton}, 
	 * and {@link #showWriteDataElement}.
	 * 
	 * default= true
	 * @type Boolean
	 */	
	showCancelWriteDataButton:		true,
	
	/**The id referencing the cancel write data button.  This is 
	 * useful for CSS customizations.
	 * 
	 * default = cancelWriteDataButton
	 * @type String
	 */		
	cancelWriteDataButtonId:	"cancelWriteDataButton",
	
	/**The text on the cancel write button.
	 * 
	 * @type String
	 */		
	cancelWriteDataButtonText:  "Cancel Write",
	
	/**The function called when data is successfully writte to
	 * the device.  This method takes two parameters:
	 *  success {Boolean} - true if data was written
	 *  display {Garmin.DeviceDisplay} - the current instance of the DeveiceDisplay
	 * @type Function
	 */				
	afterFinishWriteToDevice:	null, //function(success, display) {},
	
	/**Array of filters to sequencialy apply to activities before being sent or displayed.
	 * 
	 * @see #Garmin.FILTERS
	 * @type Array
	 */				
	dataFilters:				[],	//[Garmin.FILTERS.historyOnly]
	
	/**The function called by the display in order to acquire the data
	 * that will be written to the device during the writing operation.
	 * 
	 * This function should return a String.
	 * 
	 * @see #getWriteDataFileName
	 * @type Function
	 */
	getWriteData:				null, //function() { return $("myTextAreaId").value; },
	
	/**The function called by the display in order to acquire the filename
	 * of the data that will be written to the device during the writing 
	 * operation.
	 * 
	 * This function should return a String.
	 * 
	 * @see #getWriteData
	 * @type Function
	 */
	getWriteDataFileName:		function(){ return "myData.gpx"; },
	
	/**The function called by the display in order to acquire the data
	 * that will be written to the device during the writing operation.
	 * 
	 * This function should return an array of strings where adjacent items
	 * indicate the source (URL) of the gpi to be written and the destination
	 * (device path and filename) to write to the device.
	 *
	 * e.g.: [SOURCE,DESTINATION,SOURCE2,DESTINATION2] add as many source/destination 
	 * pairs as you'd like.
	 * 
	 * 
	 * @type Function
	 */
	getGpiWriteDescription:		null, //function() { return ["http://connect.garmin.com/SampleGpi.gpi", "Garmin\\POI\\Test.gpi"] },
	
	/**Tells the plug-in what data type to write to the device.  
	 * Options are "gpx" which will use {@link #getWriteData} to get the data
	 * or "gpi" which will use {@link #getGpiWriteDescription} to get the data to
	 * save to the device.
	 *
	 * default = "gpx"
	 * @see #showWriteDataElement
	 * @see #getWriteData
	 * @see #getGpiWriteDescription
	 * @type String
	 */		
	writeDataType:	"gpx",

	// ================== Internationalization ======================
	/** Status message exposed for internationalization. @type String */
	pluginUnlocked: "Plug-in initialized.  Find some devices to get started.",
	/** Status message exposed for internationalization. @type String */
	pluginNotUnlocked: "The plug-in was not unlocked successfully",
	/** Read data selection option exposed for internationalization. @type String */
	gpsData: "GPS Data",
	/** Read data selection option exposed for internationalization. @type String */
	trainingData: "Training Data",
	/** Status message exposed for internationalization. @type String */
	using: "Using ",
	/** Track list box item template exposed for internationalization. @type String */
	trackListing: "#{date} (Duration: #{duration})",
	/** Status message template exposed for internationalization. @type String */
	dataFound: "#{routes} routes, #{tracks} tracks and #{waypoints} waypoints found",
	/** Status message exposed for internationalization. @type String */
	writingToDevice: "Writing data to to the device",
	/** Status message exposed for internationalization. @type String */
	writtenToDevice: "Data written to the device",
	/** Status message exposed for internationalization. @type String */
	writingCancelled: "Writing cancelled",
	/** Status message exposed for internationalization. @type String */
	overwritingFile: "Overwriting file",
	/** Status message exposed for internationalization. @type String */
	notOverwritingFile: "Will not be overwriting file",
	/** Status message exposed for internationalization. @type String */
	lookingForDevices: "Looking for connected devices...",
	/** Status template exposed for internationalization. When single device is found. @type String */
	foundDevice: "Found #{deviceName}",
	/** Status template exposed for internationalization. When multiple devices are found. @type String */
	foundDevices: "Found #{deviceCount} devices",
	/** Status message exposed for internationalization. @type String */
	findCancelled: "Find cancelled",
	/** Status message exposed for internationalization. @type String */
	dataReadProcessing: "Data read from device. Processing...",
	/** Error message exposed for internationalization. @type String */
	noParseSupportForType: "The plugin does not have parsing support for file type ",
	/** Request message exposed for internationalization. @type String */
	installNow: "Install now?",
	/** Request message exposed for internationalization. @type String */
	downloadAndInstall: "Download and install now",
	/** Powered-by message. Required for plugin license agreement. @type String */
	poweredByGarmin: "Powered by <a href='http://www.opencaching.pl' target='_new'>OpenCaching PL</a> <a href='http://www.garmin.com/products/communicator/' target='_new'>Garmin Communicator</a>"
};

/*
 * DisplayBootstrap - not sure what form this should take: class or global var 
 * It should probably be in the Garmin namesapce.
 * 
 * Dynamic include of required libraries and check for Prototype
 * Code taken from scriptaculous (http://script.aculo.us/) - thanks guys!
 */
var GarminDeviceDisplay = {
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
			throw("GarminDeviceDisplay requires the Prototype JavaScript framework >= 1.5.0");
		}

		$A(document.getElementsByTagName("script"))
		.findAll(
			function(s) {
				return (s.src && s.src.match(/GarminDeviceDisplay\.js(\?.*)?$/))
			}
		)
		.each(
			function(s) {
				var path = s.src.replace(/GarminDeviceDisplay\.js(\?.*)?$/,'../../');
				var includes = s.src.match(/\?.*load=([a-z,]*)/);
				var dependencies = 'garmin/device/GarminDeviceControl' +
									',garmin/device/GarminDevicePlugin' +
									',garmin/device/GarminGpsDataStructures' +
									',garmin/device/GoogleMapController' +
									',garmin/device/GarminDevice' +
									',garmin/device/GarminPluginUtils' +
									',garmin/util/Util-XmlConverter' +
									',garmin/util/Util-Broadcaster' +
									',garmin/util/Util-DateTimeFormat' +
									',garmin/util/Util-BrowserDetect' +
									',garmin/util/Util-PluginDetect' +
									',garmin/device/GarminObjectGenerator' +
									',garmin/activity/GarminMeasurement' +
									',garmin/activity/GarminSample' +
									',garmin/activity/GarminSeries' +
									',garmin/activity/GarminActivity' +
									',garmin/activity/GarminActivityFilter' +
									',garmin/activity/TcxActivityFactory' +									
									',garmin/activity/GpxActivityFactory';
			    (includes ? includes[1] : dependencies).split(',').each(
					function(include) {
						GarminDeviceDisplay.require(path+include+'.js') 
					}
				);
			}
		);
	}	
}

GarminDeviceDisplay.load();
