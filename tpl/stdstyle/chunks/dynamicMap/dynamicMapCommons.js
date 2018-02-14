
/**
 * We are not allowed to display Google-content on non-Google-maps
 * We recived correspondence from Google Maps Team about it.
 * @param map - currently used Google map object
 */
function controlGoogleContent(map){
    var type = map.getMapTypeId(); //current map type
    var googleTypes = $.map(google.maps.MapTypeId,
                            function(value, index) { return [value]; });

    if( -1 != $.inArray(type, googleTypes) ){
        // This is google-map - display Google content
        map.setOptions({streetViewControl: true}); // streetview guy

        $("#search_control").show(); // curently search uses google.maps.Geocoder()
                                     // which licence allow to presents results
                                     // only on native Google maps

        $(".gmnoprint a, .gmnoprint span, .gm-style-cc").css("display","block");

        $("a[href*='maps.google.com/maps']").show();
    }else{
        // This is non-google content - hide google content
        map.setOptions({streetViewControl: false}); // streetview guy

        $("#search_control").hide(); // curently search uses google.maps.Geocoder()
                                     // which licence allow to presents results
                                     // only on native Google maps

        // hide logo for non-g-maps
        $("a[href*='maps.google.com/maps']").hide();

        // hide term-of-use for non-g-maps
        $(".gmnoprint a, .gmnoprint span, .gm-style-cc").css("display","none");

    }
}

