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
        title: markerModel.wp_oc +': '+ markerModel.name,
      });

      return marker;
    },

    infoWindowFactory: function( markerModel ){
      var iw = new google.maps.InfoWindow({
        content: this.infoWindowContent( markerModel ),
        maxWidth: 350
      });

      return iw;
    },

    infoWindowContent: function( markerModel ) {
      var el = $('<div></div>').loadTemplate(
        $("#<?=$markerInstance->getKey()?>"),
        {
            author: 'KESZ!',
            date: '25th May 2013',
            authorPicture: 'Authors/JoeBloggs.jpg',
            post: 'This is the contents of my post'
        });

      return el.html();
    },


    data: <?=json_encode($markersData, JSON_PRETTY_PRINT)?>,

}

<?php
};
//end of chunk - nothing should be after this line
