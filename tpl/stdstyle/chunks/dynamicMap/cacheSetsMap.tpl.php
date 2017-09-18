<?php

use lib\Objects\ChunkModels\DynamicMap\CacheSetsMapModel;

/**
	This is flavour-object for map of cacheSets.
	It handle markers with
*/

return function (CacheSetsMapModel $model){

    $iconPath = "";//GeoCacheCommons::ICON_PATH;
    $logIconPath = ""; //GeoCacheLogCommons::ICON_PATH;

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

<?php
/*
      var t = '<div class="iw-container">' +
              '  <div class="iw-title">' +
              '    <a href="/viewcache.php?wp=' + dataRow.wp_oc + '" target="_blank">' +
              '      <img class="iw-icon" src="<?=$iconPath?>' + dataRow.icon + '">' +
              '      <span class="iw-wp">'+dataRow.wp_oc+':</span>' +
              '      <span class="iw-name">'+dataRow.name+'</span>' +
              '    </a>' +
              '  </div>';

      t +=    '  <div class="iw-log">';

      if(dataRow.llog_id){ //there is a log to display
        t += '     <img src="<?=$logIconPath?>'+dataRow.llog_icon+'" ' +
                        'title="'+dataRow.llog_type_name+'" alt="'+dataRow.llog_type_name+'">';
        t += '     <a class="iw-logUsername" target="_blank"' +
                      'href="/viewprofile.php?userid='+dataRow.llog_user_id+'">';
        t +=          dataRow.llog_username+'</a>:';
        t += '     <span class="iw-logText">'+dataRow.llog_text+'</span>';
      }else{ // there is no log to display
        t += '     <?=tr('usrWatch_noLogs')?>';
      }

      t +=    '  </div>';
      t +=    '</div>';
*/?>
      return "hello";

    },

    data: <?=json_encode($model->getDataRows(), JSON_PRETTY_PRINT)?>

}

<?php
};

