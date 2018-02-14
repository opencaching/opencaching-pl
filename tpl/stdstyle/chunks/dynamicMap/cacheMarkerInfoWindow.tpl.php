    <div data-content="author"></div>
    <div data-content="date"></div>
    <div data-content="post"></div>


    <!--

        infoWindowContent: function(dataRow){

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

      return t;

    },


     -->