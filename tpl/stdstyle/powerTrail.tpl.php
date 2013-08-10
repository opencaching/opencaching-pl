<?php 
// 050242-blue-jelly-icon-natural-wonders-flower13-sc36.png
// <script src="tpl/stdstyle/js/jquery-2.0.3.js"></script>
?>
<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>

<link rel="stylesheet" href="tpl/stdstyle/js/jquery_1.9.2_ocTheme/themes/cupertino/jquery.ui.all.css">

<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.core.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.datepicker.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.datepick-{language4js}.js"></script>

<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    width: 600,
    height: 350,
    menubar: false,
	toolbar_items_size: 'small',
    language : "{language4js}",
    toolbar1: "newdocument | styleselect formatselect fontselect fontsizeselect",
    toolbar2: "cut copy paste | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image code | preview ",
    toolbar3: "bold italic underline strikethrough |  alignleft aligncenter alignright alignjustify | hr | subscript superscript | charmap emoticons | forecolor backcolor | nonbreaking ",

     plugins: [
        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons template textcolor paste textcolor"
     ],
 });
 
</script>
<script type="text/javascript"> 
<!--
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['pl']);
    $('#powerTrailDateCreatedInput').datepicker({
		dateFormat: 'yy-mm-dd',
		regional: '{language4js}'
	}).val();
    $('#commentDateTime').datepicker({
		dateFormat: 'yy-mm-dd',
		regional: '{language4js}'
	}).val();
	ajaxGetComments(0, 8);
}); 

function ajaxAddComment(){
	
	// // event.preventDefault();
	var newComment = tinyMCE.activeEditor.getContent();
	
	request = $.ajax({
    	url: "powerTrail/ajaxAddComment.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), text: newComment, type: $('#commentType').val(), datetime: $('#commentDateTime').val() },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	// $('#ptComments').html(response);
        console.log("Hooray, it worked!"+response);
    });
    toggleAddComment();
    ajaxGetComments(0, 8);
}

function toggleAddComment(){
	// event.preventDefault();
	if ($('#toggleAddComment').is(":visible")){
		$('#toggleAddComment').fadeOut(800);
		$(function() {
			setTimeout(function() {
		    	$('#addComment').fadeIn(800);
		    }, 801);
		$('html, body').animate({
        	scrollTop: $("#animateHere").offset().top
    	}, 2000);    
		});
	} else {
		$('#addComment').fadeOut(800);
		$(function() {
			setTimeout(function() {
		    	$('#toggleAddComment').fadeIn(800);
		    }, 801);
		});	
	}
}

function ajaxGetComments(start, limit){
	request = $.ajax({
    	url: "powerTrail/ajaxGetComments.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), start: start, limit: limit },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$('#ptComments').html(response);
        console.log("Hooray, it worked!"+response);
    });
}

function toggleSearchCacheSection(){
	// event.preventDefault();
	if ($('#toggleSearchCacheSection2').is(":visible")){
		$('#toggleSearchCacheSection2').fadeOut(800);
		$('#toggleSearchCacheSection0').fadeOut(800);
		
		$(function() {
			setTimeout(function() {
	       		$('#searchCacheSection').fadeIn(800);
	       		$('#toggleSearchCacheSection1').fadeIn(800);
	    	}, 801);
		});
	} else {
   		$('#searchCacheSection').fadeOut(800);
   		$('#toggleSearchCacheSection1').fadeOut(800);
		$(function() {
			setTimeout(function() {
				$('#toggleSearchCacheSection2').fadeIn(800);
				$('#toggleSearchCacheSection0').fadeIn(800);
				$('#newCacheName').html('');
				$('#newCacheNameId').val('');
				$('#CacheWaypoint').val('OP');
	    	}, 801);
		});
	}
	
}
function ajaxAddOtherUserCache(){
	$('#AloaderNewCacheAdding').show();
	$('#searchCacheSection').fadeOut(500);
	var newCacheId = $('#newCacheNameId').val();
	//ajax add cache
	
	
	request = $.ajax({
    	url: "powerTrail/ajaxAddCacheToPt.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), cacheId: newCacheId },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$("#AloaderNewCacheAddingOKimg").fadeIn(800);
    	$(function() {
	    	setTimeout(function() {
       			$("#AloaderNewCacheAddingOKimg").fadeOut(1000)
    		}, 3000);
		});
        console.log("Hooray, it worked!"+response);
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
    	toggleSearchCacheSection();
    	$('#AloaderNewCacheAdding').hide();
    	
    });

    // prevent default posting of form
    // event.preventDefault();

	return false;
	
	
}

function checkCacheByWpt(){
	$('#newCache2ptAddButton').hide();
	$('#newCacheName').html('');
	var waypoint = $('#CacheWaypoint').val();
	if(waypoint.length >= 6) {
			// alert(waypoint);
			var cacheName = ajaxRetreiveCacheName(waypoint);
			// alert(cacheName); 
	}
}

function ajaxRetreiveCacheName(waypoint) {

	$('#AloaderNewCacheSearch').show();

	request = $.ajax({
    	url: "powerTrail/ajaxRetreiveCacheName.php",
    	type: "post",
    	data:{waypoint: waypoint },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	var cacheInfoArr = response.split('!1@$%3%7%4@#23557&^%%4#@2$LZA**&6545$###');
		$('#AloaderNewCacheSearch').hide();
		$('#newCacheName').html(cacheInfoArr[0]);
		$('#newCacheNameId').val(cacheInfoArr[1]);
		if (cacheInfoArr[1] != ''){
			$('#newCache2ptAddButton').fadeIn(500);
		}       
        console.log("Hooray, it worked! "+response);
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        $('#AloaderNewCacheSearch').hide();
        console.error("The following error occured: "+textStatus, errorThrown);
    });
	
return false;
}

function ajaxUpdatType(){
	// event.preventDefault();
	$("#ptTypeNameEdit").hide();
	$('#ajaxLoaderType').show();
	
	var newType = $("#ptType1").val();
	
	request = $.ajax({
    	url: "powerTrail/ajaxUpdateType.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), newType: newType },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$("#ptTypeOKimg").show();
    	var newTypeName = $("#ptType1 option[value='"+newType+"']").text();
    	$('#ptTypeName').html(newTypeName);
    	$(function() {
	    	setTimeout(function() {
       			$("#ptTypeOKimg").fadeOut(1000)
    		}, 3000);
		  });
       	$('#powerTrailDateCreated').html($("#powerTrailDateCreatedInput").val()); 
        console.log("Hooray, it worked!"+response);
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
   		$('#ptTypeName').fadeIn(800);
		$("#ptTypeUserActionsDiv").fadeIn(800);
		
		$('#ajaxLoaderType').hide();
    });

    // prevent default posting of form
    // event.preventDefault();

	return false;
	
	
}

function ajaxUpdateDate(){
	$("#powerTrailDateCreatedEdit").hide();
	$("#ajaxLoaderPtDate").show();
	

	request = $.ajax({
    	url: "powerTrail/ajaxUpdateDate.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), newDate: $("#powerTrailDateCreatedInput").val() },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$("#ptDateOKimg").fadeIn(800);
    	$(function() {
	    	setTimeout(function() {
       			$("#ptDateOKimg").fadeOut(1000)
    		}, 3000);
		  });
       	$('#powerTrailDateCreated').html($("#powerTrailDateCreatedInput").val()); 
        console.log("Hooray, it worked!"+response);
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
   		$("#powerTrailDateCreated").fadeIn(800);
		$("#ptDateUserActionsDiv").fadeIn(800);
		$("#ajaxLoaderPtDate").hide();
    });

    // prevent default posting of form
    // event.preventDefault();

	return false;
	
}

function togglePtTypeEdit(){
	// event.preventDefault();
	$("#ptTypeName").fadeOut(800);
	$("#ptTypeUserActionsDiv").fadeOut(800);
	setTimeout(function() {
		$("#ptTypeNameEdit").fadeIn(800);
	}, 800);
}

function togglePtDateEdit(){
	// event.preventDefault();
	$("#powerTrailDateCreated").fadeOut(800);
	$("#ptDateUserActionsDiv").fadeOut(800);
	setTimeout(function() {
       	$("#powerTrailDateCreatedEdit").fadeIn(800);
    }, 800);
}

function cancelDescEdit() {
	// event.preventDefault();
	$("#powerTrailDescriptionEdit").fadeOut(800);
	$("#editDescSaveButton").fadeOut(800);
	$("#editDescCancelButton").fadeOut(800);
	setTimeout(function() {
		$("#powerTrailDescription").fadeIn(800);
		$("#toggleEditDescButton").fadeIn(800);
	}, 801);
}
function toggleEditDesc() {
	// event.preventDefault();
	
	$('html, body').animate({
        scrollTop: $("#ptdesc").offset().top
    }, 2000);
	
	$("#powerTrailDescription").fadeOut(800);
	$("#toggleEditDescButton").fadeOut(700);
	setTimeout(function() {
		$("#powerTrailDescriptionEdit").fadeIn(800);
		$("#editDescSaveButton").fadeIn(900);
		$("#editDescCancelButton").fadeIn(1000);
	}, 801);
}
function ajaxUpdatePtDescription(){
	var ptDescription = tinymce.get('descriptionEdit').getContent();
	// tinyMCE.activeEditor.getContent()
	// alert(ptDescription);
	
	$('#ajaxLoaderDescription').show();
	$("#editDescSaveButton").fadeOut(800);
	$("#editDescCancelButton").fadeOut(800);
	
	request = $.ajax({
    	url: "powerTrail/ajaxUpdatePtDescription.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), ptDescription: ptDescription },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$("#descOKimg").show();
    	$(function() {
	    	setTimeout(function() {
       			$("#descOKimg").fadeOut(1000)
    		}, 3000);
		  });
       	$('#powerTrailDescription').html(ptDescription); 
        console.log("Hooray, it worked!"+response);
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
    	
    $('html, body').animate({
        scrollTop: $("#ptdesc").offset().top
    }, 1600);
    	$('#powerTrailDescriptionEdit').fadeOut(800);
    	$('#ajaxLoaderDescription').hide();
    	setTimeout(function() {
	    	$('#powerTrailDescription').fadeIn(800);
	    	$('#toggleEditDescButton').fadeIn(800);
		}, 801);
    });

    // prevent default posting of form
    // event.preventDefault();

	return false;
}

function cancellAddNewUser2pt(){
	// event.preventDefault();
	$('#addUser').hide();
	$('#dddx').show();
	$('.removeUserIcon').hide();
}
function ajaxRemoveUserFromPt(userId){
	
	$('#ajaxLoaderOwnerList').show();
	$('#ownerListUserActions').hide();
	
	request = $.ajax({
    	url: "powerTrail/ajaxremoveUserFromPt.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), userId: userId },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
       	$('#powerTrailOwnerList').html(response); 
       	$('#ownerListOKimg').fadeIn(500);
       	$(function() {
	    	setTimeout(function() {
       			$("#ownerListOKimg").fadeOut(1000)
    		}, 3000);
		}); 
        console.log("Hooray, it worked!"+response);
        
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
    	$('#ajaxLoaderOwnerList').hide();
    	$('#addUser').hide();
    	$('#ownerListUserActions').fadeIn(800);
    	$('.removeUserIcon').hide();
    	cancellAddNewUser2pt();
    });

    // prevent default posting of form
    // event.preventDefault();

	return false;
	
		
}

function ajaxAddNewUser2pt(ptId) {
	$('#ajaxLoaderOwnerList').show();
	$('#ownerListUserActions').hide();
	
	request = $.ajax({
    	url: "powerTrail/ajaxAddNewUser2pt.php",
    	type: "post",
    	data:{projectId: ptId, userId: $('#addNewUser2pt').val()},
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
       	$('#ownerListOKimg').fadeIn(500);
       	$('#powerTrailOwnerList').html(response);
     	$(function() {
	    	setTimeout(function() {
       			$("#ownerListOKimg").fadeOut(1000)
    		}, 3000);
		}); 
        console.log("Hooray, it worked!"+response);
        
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
    	$('#ajaxLoaderOwnerList').hide();
    	$('#addUser').fadeOut(800);
    	$('#ownerListUserActions').fadeIn(500);
    	$('.removeUserIcon').hide();
    	cancellAddNewUser2pt();
    });

    // prevent default posting of form
    // event.preventDefault();

	return false;
}

function clickShow(section, section2){
	// event.preventDefault();
	$('#'+section2).hide();
	$('#'+section).show();
	$('.removeUserIcon').show();
}

var request;
function ajaxCountPtCaches(ptId) {
	$('#ajaxLoaderCacheCount').show();
	$('#cacheCountUserActions').hide();
	request = $.ajax({
    	url: "powerTrail/ajaxCachePtCount.php",
    	type: "post",
    	data:{projectId: ptId},
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
       	$('#powerTrailCacheCount').html(response);
       	$('#cCountOKimg').fadeIn(500);
      	$(function() {
	    	setTimeout(function() {
       			$("#cCountOKimg").fadeOut(1000)
    		}, 3000);
		});       	 
        console.log("Hooray, it worked!"+response);
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
    	$('#ajaxLoaderCacheCount').hide();
    	$('#cacheCountUserActions').show();
    });

    // prevent default posting of form
    // event.preventDefault();

	return false;
	}

function ajaxAddCacheToPT(cacheId)
{
	var projectId = $('#ptSelectorForCache'+cacheId).val();
    $.ajax({
      url: "powerTrail/ajaxAddCacheToPt.php",
      type: "post",
      data: {projectId: projectId, cacheId: cacheId},
      success: function(data){
      	  // alert(data);
          $("#cacheInfo"+cacheId).show();
          
          
          $(function() {
	    	setTimeout(function() {
       			$("#cacheInfo"+cacheId).fadeOut(1000)
    		}, 3000);
		  });

          
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

<style>
#linearBg1 {
	height: 25px;
	color: #E7E5DC;
	font-family: verdana;
	font-size: 12px;
	font-weight: bold;
	padding-left:8px;
	background-color: #1a82f7; background-repeat: repeat-y; 
	background: -webkit-gradient(linear, left top, right top, from(#1a82f7), to(#2F2727)); 
	background: -webkit-linear-gradient(left, #2F2727, #1a82f7); 
	background: -moz-linear-gradient(left, #2F2727, #1a82f7); 
	background: -ms-linear-gradient(left, #2F2727, #1a82f7); 
	background: -o-linear-gradient(left, #2F2727, #1a82f7);
	-moz-border-top-right-radius: 8px;
	-webkit-border-top-right-radius: 8px;
	border-top-right-radius: 8px;
}
.userActions {
	font-family: verdana;
	font-size: 9px;
}
.inlineTd{
	padding:15px;
}
.ptTd{
	font-family: verdana;
	font-size: 12px;
	text-align:center;
}


.editPtDataButton {
	-moz-box-shadow:inset 0px 1px 0px 0px #97c4fe;
	-webkit-box-shadow:inset 0px 1px 0px 0px #97c4fe;
	box-shadow:inset 0px 1px 0px 0px #97c4fe;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #3d94f6), color-stop(1, #1e62d0) );
	background:-moz-linear-gradient( center top, #3d94f6 5%, #1e62d0 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#3d94f6', endColorstr='#1e62d0');
	background-color:#3d94f6;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #337fed;
	display:inline-block;
	color:#ffffff !important;
	font-family:arial;
	font-size:11px;
	font-weight:normal;
	padding:0px 16px;
	text-decoration:none !important;
	text-shadow:1px 1px 0px #1570cd;
}.editPtDataButton:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #1e62d0), color-stop(1, #3d94f6) );
	background:-moz-linear-gradient( center top, #1e62d0 5%, #3d94f6 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e62d0', endColorstr='#3d94f6');
	background-color:#1e62d0;
}.editPtDataButton:active {
	position:relative;
	top:1px;
}
/* This imageless css button was generated by CSSButtonGenerator.com */


</style>

<link rel="stylesheet" href="tpl/stdstyle/css/ptMenuCss/style.css" type="text/css" /><style type="text/css">._css3m{display:none}</style>

<body>
	
<input type="hidden" id="xmd34nfywr54" value="{powerTrailId}">



	
<div class="content2-pagetitle"> 
 <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geocache" title="geocache" align="middle" /> 
 {{pt001}} {powerTrailName}	
</div> 

<p>
<ul id="css3menu1" class="topmenu">
{powerTrailMenu}
</ul>
</p>



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
					{ptTypeSelector}
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
	<table border=0 width=100%>
		<tr>
			<td colspan=7 id="linearBg1">{{pt035}}</td>
		</tr>
		<tr>
			<th class="ptTd">{{pt036}}</th>
			<th class="ptTd">{{pt037}}</th>
			<th class="ptTd">{{pt038}}</th>
			<th class="ptTd">{{pt039}}</th>
			<th class="ptTd">{{pt040}}</th>
			<th class="ptTd">{{pt041}}</th>
			<th class="ptTd">{{pt042}}</th>
		</tr>
		{PowerTrails}
	</table>
</div>

<!-- display single Power trail and all conected infos -->

<br /><br />
<p>{mainPtInfo}</p>

<div style="display: {displaySelectedPowerTrail}">
	
	<table border=0 width=100%>
		<tr>
			<td width=251><img src="{powerTrailLogo}" /></td>
			<td align="center">
				{powerTrailName} [ ? TU WSTAWIĆ MAPĘ ? ]
				
			</td>
		</tr>
		<tr>
			<td colspan="3" id="linearBg1">{{pt019}}</td>
		</tr>
		<tr>
			<td>{{pt022}}</td>
			<td><span id="powerTrailCacheCount">{powerTrailCacheCount}</span><img id="cCountOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" /></td>
			<td align="right">
				<span class="userActions" id="cacheCountUserActions">{cacheCountUserActions}</span>
				<span style="display: none" id="ajaxLoaderCacheCount"><img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" /></span>
			</td>
		</tr>
		<tr>
			<td>{{pt023}}</td>
			<td>
				<span id="ptTypeName">{ptTypeName}</span>
				<img id="ptTypeOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
				<div id="ptTypeNameEdit" style="display: none">
					{ptTypesSelector}
					<a href="javascript:void(0)" onclick="ajaxUpdatType()" class="editPtDataButton">{{pt044}}</a>	
				</div>
			</td>
			<td align="right">
				<span class="userActions" id="ptTypeUserActionsDiv">{ptTypeUserActions}</span>
				<img style="display: none" id="ajaxLoaderType" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
			</td>
		</tr>
		<tr>
			<td>{{pt024}}</td>
			<td>
				<span id="powerTrailDateCreated">{powerTrailDateCreated}</span>
				<img id="ptDateOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
				<span id="powerTrailDateCreatedEdit" style="display: none">
					<input id="powerTrailDateCreatedInput" type="text" value="{powerTrailDateCreated}" maxlength="10" />
					<a href="javascript:void(0)" id="editDateSaveButton" onclick="ajaxUpdateDate()" class="editPtDataButton">{{pt044}}</a>
				</span>
			</td>
			<td align="right">
				<span class="userActions" id="ptDateUserActionsDiv">{ptDateUserActions}</span>
				<img style="display: none" id="ajaxLoaderPtDate" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
			</td>
		</tr>
		<tr>
			<td>{{pt025}}</td>
			<td>
				<span id="powerTrailOwnerList">{powerTrailOwnerList}</span>
				<img id="ownerListOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
			</td>
			<td align="right">
				<span class="userActions" id="ownerListUserActions">{ownerListUserActions}</span>
				<span style="display: none" id="ajaxLoaderOwnerList"><img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" /></span>
			</td>
		</tr>
		
		<tr>
			<td colspan="3" id="linearBg1">{{pt034}}</td>
		</tr>
		<tr>
			<td class="inlineTd" colspan="2"><span id="ptdesc"></div>
				<div id="powerTrailDescription">{powerTrailDescription}</div>
				<div id="powerTrailDescriptionEdit" style="display: none">
					<textarea id="descriptionEdit" name="descriptionEdit">{powerTrailDescription}</textarea>
				</div>
			</td>
			<td align="right" valign="bottom">
				{displayPtDescriptionUserAction}
				<span style="display: none" id="ajaxLoaderDescription"><img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" /></span>
				<a href="javascript:void(0)" id="editDescCancelButton" style="display: none" onclick="cancelDescEdit()" class="editPtDataButton">{{pt031}}</a> 
				<br /> <br />
				<a href="javascript:void(0)" id="editDescSaveButton" style="display: none" onclick="ajaxUpdatePtDescription()" class="editPtDataButton">{{pt044}}</a>
				<img id="descOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
			</td>
		</tr>
	</table>
	
	<table border=0 width=100%>
	<tr>
		<td colspan="3" id="linearBg1">{{pt020}} {powerTrailName}</td>
	</tr>
	{PowerTrailCaches}
	<tr>
		<td colspan="2">
			<span id="searchCacheSection" style="display: none">
				<input onkeyup="checkCacheByWpt()" size="6" id="CacheWaypoint" type="text" maxlength="6" value="OP" />
				<img style="display: none" id="AloaderNewCacheSearch" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
				<span id="newCacheName"></span>
				<input type="hidden" id="newCacheNameId" value="-1">
				<a href="javascript:void(0)" id="newCache2ptAddButton" style="display: none" onclick="ajaxAddOtherUserCache()" class="editPtDataButton">{{pt047}}</a>
			</span>
			<img style="display: none" id="AloaderNewCacheAdding" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
			<img id="AloaderNewCacheAddingOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
		</td>
		<td align="right">
			<div style="display: {displayAddCachesButtons}">
				<a href="powerTrail.php?ptAction=selectCaches" id="toggleSearchCacheSection0" class="editPtDataButton">{{pt049}}</a><br /><br />
				<a href="javascript:void(0)" id="toggleSearchCacheSection1" style="display: none" onclick="toggleSearchCacheSection()" class="editPtDataButton">{{pt031}}</a>
				<a href="javascript:void(0)" id="toggleSearchCacheSection2" onclick="toggleSearchCacheSection()" class="editPtDataButton">{{pt048}}</a>
			</div>
		</td>
	</tr>
	</table>
	
	<table border=0 width=100%>
		<tr>
			<td id="linearBg1">{{pt021}} {powerTrailName}</td>
		</tr>
		<tr>
			<td>{{pt015}}: {powerTrailserStats}</td>
		</tr>
	</table>
	
	<!-- power Trail comments -->
	<table border=0 width=100%>
		<tr>
			<td id="linearBg1">{{pt050}}</td>
		</tr>
	</table>


	<span id="ptComments">
	<img id="commentsLoader" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
	</span>
	<div id="animateHere"></div>
	<p style="display: {displayAddCommentSection}" align="right"><a href="javascript:void(0)" id="toggleAddComment" onclick="toggleAddComment()" class="editPtDataButton">{{pt051}}</a>&nbsp; </p>
	<div id="addComment" style="display: none">
		<textarea id="addCommentTxtArea"></textarea>
		<select id="commentType">
			<option value="1">komentarz</option>
		</select><br />
		<input type="text" id="commentDateTime" value="{date}">
		<br /><br />
		<a href="javascript:void(0)" onclick="toggleAddComment()" class="editPtDataButton">{{pt031}}</a>
		<a href="javascript:void(0)" onclick="ajaxAddComment();" class="editPtDataButton">{{pt044}}</a>
		<br /><br />
	</div>

</div>

