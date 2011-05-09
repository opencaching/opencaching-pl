<script type="text/javascript">
	        var waypoint = null;
			var type = "Geocache|";
			var sym = "Geocache";
         function load() {
        var display = new Garmin.DeviceDisplay("garminDisplay", {
            pathKeyPairsArray: ["http://opencaching.pl","7af7fd68ffbdf2ba661be044dfcd054d"],
                unlockOnPageLoad: false,
                hideIfBrowserNotSupported: false,
                showStatusElement: true,
		poweredByGarmin: "Powered by <a href='http://www.opencaching.pl' target='_new'>OpenCaching PL</a>  &  <a href='http://www.garmin.com/products/communicator/' target='_blank'>Garmin Communicator</a>",
		noDeviceDetectedStatusText: "Nie znalazłem GPSa",
		lookingForDevices: "Szukam podłączonego GPS",
                autoFindDevices: false,
                findDevicesButtonText: "Kliknij aby wysłać {wp_oc} WP do GPS",
		pluginNotUnlocked: "Plug-in nie został uaktywniony",
		writtenToDevice: "Dane zostały zapisane w GPS",
		foundDevice: "Znalazłem #{deviceName}",
		downloadAndInstall: "Pobierz i zainstaluj",
                showCancelFindDevicesButton: false,
                showDeviceSelectOnLoad: false,
                showDeviceSelectNoDevice: false,
                autoReadData: false,
                autoWriteData: true,
                showReadDataElement: false,
		writeDataButtonText: "WP zapisany w GPS",
                showProgressBar: true,              
		getWriteData: function() {   
            var waypoint = new Garmin.WayPoint("{lat}", "{long}", "0", "{wp_oc}",null,"OC PL: {cachename}","Geocache", "Traditional",null);
            var factory = new Garmin.GpsDataFactory();
            var gpx = factory.produceGpxString(null, [waypoint]);
            return gpx;
        },
        afterFinishWriteToDevice: function(success, display) {
            alert("Geocache zapisany "+(success ? "z powodzeniem" : "bez powodzenia"));
        }
        });
        }
    </script>
    <br /><center>
    <div id="garminDisplay"></div>
	