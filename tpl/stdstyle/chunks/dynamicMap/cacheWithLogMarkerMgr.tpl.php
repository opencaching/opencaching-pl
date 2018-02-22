<?php
/**
 * This is JS object containing functions used to handle markers
 * - markerFactory - creates marker based on data row
 * - infoWindowFactory - creates inforWindow based on row data
 * - data - data rows of this type
 *
 * All functions are used from within dynamic map.
 */
return function (array $markersData){

    $markerInstance = $markersData[0];
?>

{
    markerFactory: function( markerModel ){

      return new google.maps.Marker({
        position: new google.maps.LatLng(markerModel.lat, markerModel.lon),
        icon: {
          url: markerModel.icon,
          scaledSize: new google.maps.Size(20, 20),
        },
        title: markerModel.wp +': '+ markerModel.name,
      });
    },

    infoWindowFactory: function( markerModel ){
      return new google.maps.InfoWindow({
        content: this.__infoWindowContent( markerModel ),
        maxWidth: 350
      });
    },

    __infoWindowContent: function( markerModel ) {

      if(!this.__infoWindowCompiled){
        source = $('#<?=$markerInstance->getKey()?>Tpl').html();
        this.__infoWindowCompiled = Handlebars.compile(source);
      }

      var context = {
        icon:         markerModel.icon,
        link:         markerModel.link,
        name:         markerModel.name,
        wp:           markerModel.wp,
        log_link:     markerModel.log_link,
        log_text:     markerModel.log_text,
        log_username: markerModel.log_username,
        log_icon:     markerModel.log_icon,
        log_typeName: markerModel.log_typeName,
        log_userLink: markerModel.log_userLink,
      };

      return this.__infoWindowCompiled(context);
    },

    __infoWindowCompiled: null,

    data: <?=json_encode($markersData, JSON_PRETTY_PRINT)?>,

}

<?php
};
//end of chunk - nothing should be after this line
