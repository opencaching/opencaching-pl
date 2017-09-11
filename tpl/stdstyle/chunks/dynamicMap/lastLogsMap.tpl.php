<?php

use lib\Objects\ChunkModels\DynamicMap\LastLogMapModel;
use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\GeoCache\GeoCacheLogCommons;

/**
	This is flavour-object for dynamic map.
	It handle markers with 
*/

return function (LastLogMapModel $model){

    $iconPath = GeoCacheCommons::ICON_PATH;
    $logIconPath = GeoCacheLogCommons::ICON_PATH;

?>

{
    markerFactory: function(dataRow){

      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(dataRow.lat, dataRow.lon),
        icon: {
            url: '<?=$iconPath?>'+dataRow.icon,
            scaledSize: new google.maps.Size(20, 20),
          },
        title: dataRow.wp_oc+': '+dataRow.name,
      });

      return marker;
    },

    infoWindowFactory: function(dataRow){
      var iw = new google.maps.InfoWindow({
        content: this.infoWindowContent(dataRow),
        maxWidth: 350
      });

      return iw;
    },

    infoWindowContent: function(dataRow){

      var t = '<div class="iw-container">' +
              '  <div class="iw-title">' +
              '    <a href="/viewcache.php?wp='+dataRow.wp_oc+'">' +
              '      <span class="iw-wp">'+dataRow.wp_oc+':</span>' +
              '      <span class="iw-name">'+dataRow.name+'</span>' +
              '    </a>' +
              '  </div>';

      t +=    '  <div class="iw-log">';

      if(dataRow.llog_id){ //there is a log to display
        t += '     <img src="<?=$logIconPath?>'+dataRow.llog_icon+'" ' +
                        'title="'+dataRow.llog_type_name+'" alt="'+dataRow.llog_type_name+'">';
        t += '     <a class="iw-logUsername" ' +
                      'href="/viewprofile.php?userid='+dataRow.llog_user_id+'">';
        t +=          dataRow.llog_username+'</a>:';
        t += '     <span class="iw-logText">'+dataRow.llog_text+'</span>';
      }else{ // there is no log to display
        t += '     <?=tr('usrWatch_noLogs')?>';
      }

      t +=    '  </div>';
      t +=    '</div>';

      return t;

    },

    data: <?=json_encode($model->getDataRows(), JSON_PRETTY_PRINT)?>

}

<?php
};

