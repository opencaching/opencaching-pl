<style>
	a.info{
	    position:relative; /*this is the key*/
	    z-index:24; background-color:#ccc;
	    color:#000;
	    text-decoration:none}

	a.info span{display: none}

    #trigger {
    	width:100px;
    	height:200px;
    	border: 1px solid #0cf;
}
   	#tooltipAnchor {
   		position: absolute;
   		top: 4em; //was 2em
   		//left: 2em;
   		width: 15em;
   		border: 1px solid #000000;
   		background-color: #cfffff;
   		text-align: center;
   	}

   	#tooltipLink {
   		z-index: 25;
   	}

</style>

<script>
	function showTip() {
		t = document.getElementById('tooltipAnchor');
  		t.style['display'] = 'block';
  		setTimeout("expireTooltip()", 3500);
	}

	function expireTooltip() {
	  t = document.getElementById('tooltipAnchor');
	  t.style['display'] = 'none';
	}
</script>


<a class="info" href="#" id="tooltipLink">This is a tooltip <span id="tooltipAnchor">an
aiding text that appears just when you roll on with the mouse</span></a>

<br><br>

<select id="theSelect" onchange="showTip();">
<option value="1">One</option>
<option value="2">Two</option>
</select>
<?php
/*
function calcLatLong($long, $lat, $distance, $bearing) {
 $EARTH_RADIUS_EQUATOR = 6378140.0;
 $RADIAN = 180 / pi();
 $b = $bearing / $RADIAN;
 $long = $long / $RADIAN;
 $lat = $lat / $RADIAN;
 $f = 1/298.257;
 $e = 0.08181922;
	
 $R = $EARTH_RADIUS_EQUATOR * (1 - $e * $e) / pow( (1 - $e*$e * pow(sin($lat),2)), 1.5);	
 $psi = $distance/$R;
 $phi = pi()/2 - $lat;
 $arccos = cos($psi) * cos($phi) + sin($psi) * sin($phi) * cos($b);
 $latA = (pi()/2 - acos($arccos)) * $RADIAN;

 $arcsin = sin($b) * sin($psi) / sin($phi);
 $longA = ($long - asin($arcsin)) * $RADIAN;
 return array('longitude' => $longA, 'latitude' => $latA);
}

print_r(calcLatLong(17, 52, 129289, 30.34));
*/
?>
