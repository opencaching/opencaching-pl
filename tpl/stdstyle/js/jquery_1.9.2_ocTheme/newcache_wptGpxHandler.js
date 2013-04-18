/**
 * script is used in newcache.php via Ajax to get data (cooedinates, name) from waypoint 
 * stored in .gpx file. (for ex. created in garmin).
 */

function ajaxFileUpload()
	{
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});

		$.ajaxFileUpload
		(
			{
				url:'newcacheAjaxWaypointUploader.php',
				secureuri:false,
				fileElementId:'fileToUpload',
				dataType: 'json',
				data:{name:'logan', id:'id'},
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert(data.error);
						}else
						{
   							obj = JSON.parse(data.msg);
							
							// document.getElementById("lat_h").value = ish;
 							$("#lat_h").val(obj.coords_lat_h);
 							$("#lon_h").val(obj.coords_lon_h);
 							$("#lat_min").val(obj.coords_lat_min);
 							$("#lon_min").val(obj.coords_lon_min);
							$("#name").val(obj.name);
							$("#desc").val(obj.desc);
							// alert(data.msg);
						}
					}
				},
				error: function (data, status, e)
				{
					alert(e);
				}
			}
		)
		return false;
	}