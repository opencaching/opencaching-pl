<?php 
// 050242-blue-jelly-icon-natural-wonders-flower13-sc36.png
?>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript"> 
<!--
function ajaxAddCacheToPT(cacheId)
{
	var projectId = $('#ptSelectorForCache'+cacheId).val();
    $.ajax({
      url: "powerTrail/ajaxAddCacheToPt.php",
      type: "post",
      data: {projectId: projectId, cacheId: cacheId},
      success: function(data){
      	  alert(data);
          $("#cacheInfo"+cacheId).show();
      },
      error:function(){
          alert("failure");
      }   
    }); 
}


function toggle() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText1");
	var text2 = document.getElementById("displayText2");
	var os_tytul = document.getElementById("os_tytul");
	var help_link1 = document.getElementById("help_link1");
	var help_link2 = document.getElementById("help_link2");
	var cialo = document.getElementById("cialo");
	
	if(ele.style.display == "block") 
	 {
      ele.style.display = "none";
	  // os_tytul.style.display = "block";
	  text.innerHTML = "{{os_zobo}}";
	  text2.innerHTML = "{{os_zobo}}";
	  help_link1.style.display = "block";
	  help_link2.style.display = "none";
	  cialo.style.display = "block";
  	 }
	else 
	 {
	  ele.style.display = "block";
	  // os_tytul.style.display = "none";
	  text.innerHTML = "{{os_powrot}}";
	  text2.innerHTML = "{{os_powrot}}";
	  help_link1.style.display = "none";
	  help_link2.style.display = "block";
	  cialo.style.display = "none";
	 }
} 
// -->
</script>

<body>
	
	

	
<div class="content2-pagetitle"> 
 <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geocache" title="geocache" align="middle" /> 
 {{pt001}} {powerTrailName}	
</div> 

{powerTrailMenu}


<div style="display: {displayCreateNewPowerTrailForm}">
	<form name="createNewPowerTrail" action="powerTrail.php" method="post">
		<table>
			<tr>
				<td>{{pt008}} </td>
				<td><input type="text" name="powerTrailName" /></td>
			</tr>
			<tr>
				<td>{{pt009}}</td>
				<td>
				<select name="type">
					<option value="1">{{pt004}}</option>
					<option value="2">{{pt005}}</option>
				</select>
				</td>
			</tr>
			<tr>
				<td>{{pt010}}</td>
				<td>		
					<select name="status">
						<option value="1">{{pt006}}</option>
						<option value="2">{{pt007}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{{pt011}}</td>
				<td>		
					<textarea name="description"></textarea>
				</td>
			</tr>
		<tr>
			<td><input type="submit" value="{{submit}}" name="createNewPowerTrail" /></td>
		</tr>
		</table>
	</form>
</div>

<div style="display: {displayUserCaches};">
	<div class="searchdiv">
		<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
		<tr>
		 <td><a href="{os_script}?sort=wpt">waypoint</a></td>
		 <td><a href="{os_script}?sort=nazwa">{{cache_name}}</a></td>
		 <td><a href="{os_script}?sort=autor">{{pt002}}</a></td>
		</tr>
	   <tr>
        <td colspan="7"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="1" width="100%"/></td>
      </tr>
		{keszynki}
      <tr>
       <td colspan="7"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="5" width="100%"/></td>
      </tr>
      </table>
    </div>
</div>

<div style="display: {displayPowerTrails}">
	<table>
	{PowerTrails}
	</table>
</div>

<!-- display single Power trail and all conected infos -->

<br /><br />
<p>{mainPtInfo}</p>

<div style="display: {displaySelectedPowerTrail}">
	
	<table>
		<tr>
			<td><img src={powerTrailLogo} /></td>
			<td>{powerTrailLogo} {powerTrailName}</td>
		</tr>
	</table>
	
	<table>
	{PowerTrailCaches}
	</table>
	<p>{{pt015}}: {powerTrailserStats}</p>
</div>