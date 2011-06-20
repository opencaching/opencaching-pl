<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to OpenCaching PL Node API</title>
<link rel="stylesheet" type="text/css" media="screen,projection" href="/tpl/stdstyle/css/style_screen.css" />
<link rel="SHORTCUT ICON" href="favicon.ico" />
</head>
<body style="background-color:#ddebf5;">

<div class="overall">
  <div class="page-container-1" style="position: relative;">
  <div id="bg1">
  &nbsp;
  </div>
  <div id="bg2">
  &nbsp;
  </div>
<div id="logo" style="margin-left: 50px;"><a href="../services"><img src="/images/oc_logo.png" alt="" style="margin-top:15px; margin-left:3px;" /></a>
<span style="margin-left:5px;margin-top:10px;font-weight:bold;font-size: 20px;">Welcome to OpenCaching PL Node API </span></div></br>
<div style="margin-left: 50px;">
<div class=logs style="width:95%">
</br><span style="margin-left:10px;font-weight:bold;font-size:15px;">GET geocache requests are made to read data.</span></br></br>
</div>
<div class="searchdiv">

	<div>
		<p>Returns a list of geocaches.</p>
		<p>Either area, point, wp name, or owner are required.</p>

		<p>Results are sorted by distance from the center point. If not center point is provided, then they are sorted by the distance from the center of the bbox. If neither of those are provided, then they are sorted by oxcode.
	</div>
	<div>
		<h4>area=west_longitude,south_latitude,east_longitude,north_latitude</h4>
			<p>Returns a list of geocaches inside the specified bounding box sorted by the distance from the center of the area.</p>
			<p>The parameters west longitude, south latitude, east longitude,north latitude define the edges of a bounding area.</p>
			<p>Coordinates should be in decimal degrees</p>
			<p>Use positive numbers for north latitude and east longitude and negitive numbers of south latitude and west longitude.</p>
	</div>

	<div>

		<h4>point=lat,lon</h4>
			<p>Returns a list of geocaches closest to the specified point side sorted by the distance from that point.</p>
	</div>
	<div>
		<h4>dist=xx whrere xx is distance in km</h4>
			<p>Returns a list of geocaches closest to the specified point side sorted by the distance from that point.It is use with point parameteres</p>
	</div>
	<div>
		<h4>modifiedsience=date whrere date is format yyyyddhhmmss </h4>
			<p>Returns a list of geocaches which were created or modified after the date</p>
	</div>

	<div>
		<h4>wp=waypoint1,waypoint2 example: wp=OP012H,OPA23E</h4>
			<p>Limits returned geocaches to those caches that have an waypoint name that is in the list.</p>
	</div>
	

	<div>
		<h4>difficulty=min_difficulty-max_difficulty</h4>
			<p>Limits returned geocache to those with a difficulty rating between min_difficulty and max_difficulty inclusive.</p>
			<p>Min and max difficulty are decimal numbers that can range from 1 to 5</p>
	</div>

	<div>
		<h4>terrain=min_terrain-max_terrain</h4>
			<p>Limits returned geocache to those with a terrain rating between min_terrain and max_terrain inclusive.</p>
			<p>Min and max terrain are decimal numbers that can range from 1 to 5</p>
	</div>

	<div>
		<h4>score=min_score-max_score</h4>

			<p>Limits returned geocache to those with a score rating between min_score and max_score inclusive.</p>
			<p>Min and max score are decimal numbers that can range from 1 to 5</p>
	</div>

	<div>
		<h4>recommend=min_recommend-max_recommend</h4>

			<p>Limits returned geocache to those with a recommendations between min_recommend and max_recommend inclusive.</p>
			<p>Min and max recommend are decimal numbers</p>
	</div>

	<div>
		<h4>size=min_size-max_size</h4>
			<p>Limits returned geocache to those with a size rating between min_size and max_size inclusive.</p>

			<p>Min and max size are decimal numbers that can range from 1 to 5</p>
	</div>

	<div>
		<h4>found=true/false</h4>
			<p>A user must be logged in for this parameter to have any affect.</p>
			<p><em>found = true:</em><br>Only geocaches the user has already logged as found will be returned.</p>

			<p><em>found = false:</em><br>Only geocaches the user has not already logged as found will be returned.</p>
			<p><em>found not specified:</em><br>Both geocaches the user and marked as found and those not marked as found will be return.</p>
	</div>

	<div>
		<h4>type=type1,type2 example: type=2,4 </h4>
			<p>List of the types of geocaches to be returned. If no type parameter is specificed, all types are returned. Otherwise, only the listed types are returned.</p>
                        <ul>
			<p>1- <em>Other cache</em></p> 
			<p>2- <em>Traditional cache</em></p>
			<p>3- <em>Multi-cache</em></p>
			<p>4- <em>Virtual cache</em></p> 
			<p>5- <em>Webcam cache</em></p> 
			<p>6- <em>Event cache</p> 
			<p>7- <em>Quiz cache</em></p>
			<p>8- <em>Moving cache</em></p>
			<p>9- <em>Own cache</em></p> 
			</ul>
	</div>

	<div>
		<h4>attrib=attribute1,attribute2: example: attrib=3,5,12 ???????????? make sens ???</h4>
			<p>List of the atrtributes id of geocaches.</p>
                        <ul>
			<p><em>40</em> - One-minute cache</p>
			<p><em>41</em> - Go geocaching with children</p>
			<p><em>43</em> - GeoHotel</p>
			<p><em>44</em> - Accessible for disabled</p>
			<p><em>47</em> - Compass</p>
			<p><em>49</em> - Take something to write</p>
			</ul>
	</div>

	<div>
		<h4>limit=limit</h4>

			<p>Sets the maximum number of geocaches that will be returned. Can be between 0 and 1000. Defaults to 100 if no limit is specified.</p>
	</div>

	<div>
		<h4>desc=true/false</h4>
			<p><em>desc = true:</em><br>Cache descriptions and hints are returned for all geocaches.</p>
			<p><em>desc = false:</em><br>Cache descriptions and hints are not returned.</p>
			<p>Defaults to false for JSON requests.</p>

			<p>This option is not currently available for GPX requests.</p>
			<p>Avoid using desc=true unless you really need the description and hint for every geocache. Getting the geocache descriptions can triple (or more) the size of the returned data. When possible get a list of geocaches without descriptions, and then get the descriptions for individual geocaches as necessary.</p>
	</div>

	<div>
		<h4>logs=log_limit</h4>
			<p>The number of logs that will be returned with each geocache. Defaults to 0.</p>

	</div>

	<div >
		<h4>owner=user_id1,user_id2,user_id3 exmaple: owner=2034,1045 </h4>
			<p>Return only geocaches that were hidden by the specified owner cache users.</p>	
	</div>

	<div>
		<h4>ignore_my=true</h4>
			<p>A user must be logged in for this parameter to have any affect.</p>
			<p><em>skip_my = true:</em><br>Skip geocaches the user has already logged to API will be returned.</p>
	</div>



	<div >
		<h4>format=output_format_name exmaple: format=json </h4>
			<p>Returns the requested geocache in defined output format. Formats allowed: xml,json,gpx default format is gpx.</p>	
	</div>

</div>
</br>
</br/>
<br/>
</body>
</html>
