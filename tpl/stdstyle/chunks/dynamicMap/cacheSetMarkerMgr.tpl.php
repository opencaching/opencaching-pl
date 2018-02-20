<?php

return function (array $markersData){

    $markerInstance = $markersData[0];

?>

{
    markerFactory: function( markerModel ){

      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(markerModel.lat, markerModel.lon),
        icon: {
            url: markerModel.icon,
            scaledSize: new google.maps.Size(20, 20),
          },
        title: markerModel.name,
      });

      return marker;
    },

    infoWindowFactory: function( markerModel ){
      var infoWin = new google.maps.InfoWindow({
        content: this.infoWindowContent( markerModel ),
        maxWidth: 350
      });

      return infoWin;
    },

    infoWindowContent: function( markerModel ) {

      if(!this.infoWindowCompiled){
        source = $('#<?=$markerInstance->getKey()?>Tpl').html();
        this.infoWindowCompiled = Handlebars.compile(source);
      }

      var context = {
        link: markerModel.link,
        name: markerModel.name,
      };

      return this.infoWindowCompiled(context);

    },

    infoWindowCompiled: null,

    data: <?=json_encode($markersData, JSON_PRETTY_PRINT)?>,

}

<?php
};
//end of chunk - nothing should be after this line
