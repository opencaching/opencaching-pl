
<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/search1.png" class="icon32" alt="{title_text}" title="{title_text}" align="middle" />
  &nbsp;{title_text}
</div>

<div class='searchdiv' >
    <div id="mapka" style="width:100%; height:500pt; text-align:left;"></div>
</div>



<script type="text/javascript">
    var hmapa = null;
            var currentinfowindow = null;
            var weatherLayer = null;
            function SwitchWeather()
            {
                if (weatherLayer.getMap() == null)
                { weatherLayer.setMap(hmapa); }
                else
                { weatherLayer.setMap(null); }
            }

    function SavePosition()
    {
    alert("{{commit_watch}}");
            var LatLlng = hmapa.getCenter();
            var weather = 0;
            if (weatherLayer.getMap() != null)
            weather = 1;
            window.location.href = "mywatches.php?rq=map&wcMapZoom=" + hmapa.getZoom()
            + "&wcMapLatitude=" + LatLlng.lat()
            + "&wcMapLongitude=" + LatLlng.lng()
            + "&wcMapWeather=" + weather;
    }


    function BlockControl(controlDiv, map, text, fun)
    {

    // Set CSS styles for the DIV containing the control
    // Setting padding to 5 px will offset the control
    // from the edge of the map
    controlDiv.style.padding = '5px';
            controlDiv.className = "gmnoprint gm-style-mtc";
            //controlDiv.style.boxShadow="0px 1px 4px -1px #888888";

            /*<div class="gmnoprint gm-style-mtc" style="margin: 5px; z-index: 0; position: absolute;
             cursor: pointer; text-align: left; width: 85px; right: 0px; top: 0px;">
             <div style="direction: ltr; overflow: hidden; text-align: left;
             position: relative; color: rgb(0, 0, 0); font-family: Roboto,Arial,sans-serif;
             -moz-user-select: none; font-size: 11px; background-color: rgb(255, 255, 255);
             padding: 1px 6px; border-radius: 2px; background-clip: padding-box;
             border: 1px solid rgba(0, 0, 0, 0.15); box-shadow: 0px 1px 4px -1px rgba(0, 0, 0, 0.3);
             font-weight: 500;" draggable="false" title="Zmień styl mapy">*/

            // Set CSS for the control border
            var controlUI = document.createElement('div');
            controlUI.style.backgroundColor = 'white';
            controlUI.style.borderStyle = 'solid';
            controlUI.style.borderWidth = '1px';
            controlUI.style.borderColor = 'gray';
            controlUI.style.cursor = 'pointer';
            controlUI.style.textAlign = 'center';
            controlUI.style.boxShadow = "0px 1px 4px -1px #888888";
            controlUI.title = '';
            controlDiv.appendChild(controlUI);
            // Set CSS for the control interior
            var controlText = document.createElement('div');
            //controlText.style.fontFamily = 'Arial,sans-serif';
            controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
            controlText.style.fontSize = '11px';
            controlText.style.fontWeight = '500';
            //controlText.style.color = 'black';
            controlText.style.paddingLeft = '5px';
            controlText.style.paddingRight = '5px';
            controlText.innerHTML = text;
            controlUI.appendChild(controlText);
            google.maps.event.addDomListener(controlUI, 'click', function() {
            fun();
            });
            google.maps.event.addDomListener(controlUI, 'mouseover', function() {
            controlUI.style.backgroundColor = '#dddddd';
            });
            google.maps.event.addDomListener(controlUI, 'mouseout', function() {
            controlUI.style.backgroundColor = 'white';
            });
    }

    function AddMarker(wspolrzedne, icon, cache_icon, wp, cache_name, log_id, log_icon, user_id, user_name, log_date, log_text)
//function AddMarker(wspolrzedne, icon )
    {
    var marker = new google.maps.Marker({
    position: wspolrzedne,
            map: hmapa,
            icon: icon

    });
            var textContent;
            if (log_date == "")
            textContent = '<table><tr><td><img src=\"tpl/stdstyle/images/' + cache_icon + '\" border=\"0\" alt=\"\" title=\"geocache\"/><b>&nbsp;<a class=\"links\" href=\"viewcache.php?wp=' + wp + '\">' + wp + ': ' + cache_name + '</a></td></tr><tr><td><span style = \"font-size: 8pt\">Brak wpisu</span></td></tr></table>';
            else
            textContent = '<table><tr><td><img src=\"tpl/stdstyle/images/' + cache_icon + '\" border=\"0\" alt=\"\" title=\"geocache\"/><b>&nbsp;<a class=\"links\" href=\"viewcache.php?wp=' + wp + '\">' + wp + ': ' + cache_name + '</a></td></tr><tr><td><a class=\"links\" href=\"viewlogs.php?logid=' + log_id + '\"><img src=\"tpl/stdstyle/images/' + log_icon + '\" border=\"0\" alt=\"\" /></a> <span style = \"links\"> {{mywatches_map_01}} </span> <a class=\"links\" href=\"viewprofile.php?userid=' + user_id + '\">' + user_name + '</a> <span style = \"links\">{{mywatches_map_02}}: ' + log_date + '</span><hr><span style = \"font-size: 8pt\">' + log_text + '</span></td></tr></table>';
            var infowindow = new google.maps.InfoWindow({
            content: textContent
            });
            google.maps.event.addListener(marker, "click", function() {
            if (currentinfowindow !== null) {
            currentinfowindow.close();
            }
            infowindow.open (hmapa, marker);
                    currentinfowindow = infowindow;
            });
    }


    function initialize()
    {
    var mapDiv = document.getElementById('mapka');
            var mapOptions = {
            zoom: {wcMapZoom},
                    center:  new google.maps.LatLng({latitude}, {longitude}),
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    disableDefaultUI: true,
                    scaleControl: true,
                    streetViewControl: true,
                    overviewMapControl: true,
                    panControl: false,
                    zoomControl: true,
                    zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL
                    },
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                    },
            };
            hmapa = new google.maps.Map(mapDiv, mapOptions);
            var homeControlDiv = document.createElement('div');
            var homeControl = new BlockControl(homeControlDiv, hmapa, '<b>{{weather}}</b>', SwitchWeather);
            var homeControl1 = new BlockControl(homeControlDiv, hmapa, '<b>{{save}}</b>', SavePosition);
            homeControlDiv.index = 1;
            hmapa.controls[google.maps.ControlPosition.TOP_LEFT].push(homeControlDiv);
            weatherLayer = new google.maps.weather.WeatherLayer({
            temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS });
            var wcMapWeather = {wcMapWeather};
            if (wcMapWeather == 1)
            weatherLayer.setMap(hmapa);
            else
            weatherLayer.setMap(null);
    {markers}
    }


    google.maps.event.addDomListener(window, 'load', initialize);

</script>



