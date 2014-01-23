/**
 * This script is used in viewcache.php to pup-up window with information
 * of automatic Log geokrety with GeoKretyApi. (if called)
 *
 * @author Andrzej Łza Woźniak
 */
$.fx.speeds._default = 500;
$(function() {

    $( "#dialog-message" ).dialog(
            {
                position: ['center',150],
                autoOpen: true,
                width: 500,
                modal: true,
                show: "blind",
                hide: "explode",
                buttons:
                {
                    Ok: function()
                    {
                        $(this).dialog( "close" );
                    }
                }
            });
});



//$