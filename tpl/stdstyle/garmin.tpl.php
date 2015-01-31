<script type="text/javascript">
    var waypoint = null;
    var type = "Geocache|";
    var sym = "Geocache";
    function load() {
        var display = new Garmin.DeviceDisplay("garminDisplay", {
            pathKeyPairsArray: [{garminKeyStr}],
            unlockOnPageLoad: false,
            hideIfBrowserNotSupported: false,
            showStatusElement: true,
            poweredByGarmin: "Powered by <a href='index.php' target='_new'>{site_name}</a>  &  <a href='http://www.garmin.com/products/communicator/' target='_blank'>Garmin Communicator</a>",
            noDeviceDetectedStatusText: "{{garmin_not_found}}",
            lookingForDevices: "{{garmin_search}}",
            autoFindDevices: false,
            findDevicesButtonText: "{{garmin_to_gps}}",
            pluginNotUnlocked: "{{garmin_plugin}}",
            writtenToDevice: "{{garmin_written}}",
            foundDevice: "{{garmin_found}} #{deviceName}",
            downloadAndInstall: "{{garmin_download}}",
            showCancelFindDevicesButton: false,
            showDeviceSelectOnLoad: false,
            showDeviceSelectNoDevice: false,
            autoReadData: false,
            autoWriteData: true,
            showReadDataElement: false,
            writeDataButtonText: "{{garmin_write}}",
            showProgressBar: true,
            getWriteData: function () {
                var waypoint = new Garmin.WayPoint("{lat}", "{long}", "0", "{wp_oc}", null, "OC PL: {cachename}", "Geocache", "Traditional", null);
                var factory = new Garmin.GpsDataFactory();
                var gpx = factory.produceGpxString(null, [waypoint]);
                return gpx;
            },
            afterFinishWriteToDevice: function (success, display) {
                alert("{{garmin_send}} " + (success ? "{{garmin_send_yes}}" : "{{garmin_send_no}}"));
            }
        });
    }
</script>
<br /><center>
    <div id="garminDisplay"></div>

