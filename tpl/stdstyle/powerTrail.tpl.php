<link href='http://fonts.googleapis.com/css?family=Shojumaru&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<link rel="stylesheet" href="tpl/stdstyle/js/jquery_1.9.2_ocTheme/themes/cupertino/jquery.ui.all.css">
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/minified/jquery-ui.min.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.datepick-{language4js}.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
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
	
	getActiveSortBy();
	ajaxGetPtCaches();
	ajaxGetComments(0, {commentsPaginateCount});
	$("#ptPercentCc").html(calculatepercentCc( $("#powerTrailpercent").html(), $("#powerTrailCacheCount").html()));
}); 


function getGpx(output){
	var showresult		= $("#showresult").val();
	
	var f_inactive;
	var f_ignored;  
	var f_userowner;
	var f_watched;  
	var f_userfound;
	var count = $("#count").val();
	var ptId = $("#ptId").val();
	$('#f_inactive').is(':checked')  ? f_inactive  = 0 : f_inactive  = 1 ;
	$("#f_ignored").is(':checked')   ? f_ignored   = 0 : f_ignored   = 1 ;
	$("#f_userowner").is(':checked') ? f_userowner = 0 : f_userowner = 1 ;
	$("#f_watched").is(':checked')   ? f_watched   = 0 : f_watched   = 1 ;
	$("#f_userfound").is(':checked') ? f_userfound = 0 : f_userfound = 1 ;
	
	
	var gpxLink = 'search.php?searchto=searchbypt&showresult=1&f_inactive='+f_inactive+'&f_ignored='+f_ignored+'&f_userfound='+f_userfound+'&f_userowner='+f_userowner+'&f_watched='+f_watched+'&count='+count+'&ptId='+ptId+'&output='+output;
	window.location = gpxLink;
}


function updateCc(){
	$("#ptPercentCc").html(calculatepercentCc( $("#demandPercent").val(), $("#powerTrailCacheCount").html()));
}
function calculatepercentCc(percent, totCacheCount){
	var ptPercentCc;
	ptPercentCc = totCacheCount * percent / 100;
	return Math.ceil(ptPercentCc);
}

function getActiveSortBy(){
	value1 = jQuery("#sortBy option:selected").html();
	value2 = jQuery("#filter option:selected").html();
	$('#activeSortBy').html(value1);
	$('#activeFilter').html(value2);
}

function showDisplayOptions() {
	if($('#displayOptionsButton').is(":visible")){
		$('#displayOptionsButton').fadeOut(800);
		$(function() {
			setTimeout(function() {
				$("#displayOptions").fadeIn(800);
	    	}, 801);
		});
	} else {
		$("#displayOptions").fadeOut(800);
		$(function() {
			setTimeout(function() {
				$('#displayOptionsButton').fadeIn(800);
	    	}, 801);
		});		
	}
}

// ?sortBy=points&filter=t&fv=2

function toggleSearchCacheSection2(){
	if ($('#toggleSearchCacheSection2').is(":visible")){
		$('#toggleSearchCacheSection2').fadeOut(800);
		$('#toggleSearchCacheSection0').fadeOut(800);
		$('#toggleSearchCacheSection3').fadeOut(800);
		$('#finalCachesbtn').fadeOut(800);
		$(function() {
			setTimeout(function() {
	       		$('#searchCacheSectionRm').fadeIn(800);
	       		$('#toggleSearchCacheSectionRm').fadeIn(800);
	    	}, 801);
		});
	} else {
   		$('#searchCacheSectionRm').fadeOut(800);
   		$('#toggleSearchCacheSectionRm').fadeOut(800);
		$(function() {
			setTimeout(function() {
				$('#toggleSearchCacheSection2').fadeIn(800);
				$('#toggleSearchCacheSection0').fadeIn(800);
				$('#toggleSearchCacheSection3').fadeIn(800);
				$('#finalCachesbtn').fadeIn(800);
				$('#newCacheName').html('');
				$('#newCacheNameId').val('');
				$('#CacheWaypoint').val('OP');
	    	}, 801);
		});
	}
	
}


function ajaxRmOtherUserCache(){
	$('#AloaderNewCacheAdding').show();
	$('#searchCacheSection2').fadeOut(500);
	var newCacheId2 = $('#newCacheNameId2').val();

	request = $.ajax({
    	url: "powerTrail/ajaxAddCacheToPt.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), cacheId: newCacheId2, rmOtherUserCacheFromPt: 1 },
	});

    request.done(function (response, textStatus, jqXHR){
    	ajaxGetPtCaches();
    	if(response == 'Removed'){
    		$("#AloaderNewCacheAddingOKimg").fadeIn(800);
    		$("#message").html('{{pt161}}');
    		$("#messageDiv").fadeIn(800);
	    	$(function() {
		    	setTimeout(function() {
	       			$("#AloaderNewCacheAddingOKimg").fadeOut(1000);
	       			$("#messageDiv").fadeOut(1000);
	    		}, 3000);
			});
		} else {
			$("#AloaderNewCacheAddingNOKimg").fadeIn(800);
			$("#message").html('{{pt160}}');
			$("#messageDiv").fadeIn(800);
	    	$(function() {
		    	setTimeout(function() {
	       			$("#AloaderNewCacheAddingNOKimg").fadeOut(1000);
	       			$("#messageDiv").fadeOut(1000);
	    		}, 3000);
			});
		}
        console.log("ajaxRmOtherUserCache finished successfully: "+response);
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    request.always(function () {
    	toggleSearchCacheSection2();
    	$('#AloaderNewCacheAdding').hide();
    	
    });

	return false;
}


function reloadWithFinalsChoice(){
	if($('#finalCachesbtn').html() == '{{pt150}}'){
		ajaxGetPtCaches();
		$('#finalCachesbtn').html( '{{pt149}}' );
	} else {
		$('#PowerTrailCaches').html('');
		ajaxGetPtCaches(1);
		$('#finalCachesbtn').html( '{{pt150}}' );
	}	
}

function setFinalCache(cacheId){
	if($('#fcCheckbox'+cacheId).is(':checked')) {
		addRmFinals(1, cacheId, $('#xmd34nfywr54').val());
	} else {
   		addRmFinals(0, cacheId, $('#xmd34nfywr54').val());
	}
	
}

function addRmFinals(isFinal, cacheId, ptId){
	// ajaxAddRmFinal.php
	request = $.ajax({
    	url: "powerTrail/ajaxAddRmFinal.php",
    	type: "post",
    	data:{isFinal: isFinal, projectId: $('#xmd34nfywr54').val(), cacheId: cacheId},
	});

    request.done(function (response, textStatus, jqXHR){
    	console.log(response);
    });
	
	request.always(function () {
		$('#PowerTrailCaches').html('');
		ajaxGetPtCaches(1);
    });
	
}

function editComment(commentId,ClickinguserId){
	commentHtml = $('#commentId-'+commentId).html();
	$('#editedCommentId').val(commentId);
	$('#ClickinguserId').val(ClickinguserId);
	// alert(commentHtml);
	$('#addC1').hide();
	$('#addC2').hide();
	$('#addCe1').show();
	$('#addCe2').show();
	
	$('#commentType').hide();
	$('#addComment').fadeIn(1200);
	tinyMCE.activeEditor.setContent(commentHtml);
	$('html, body').animate({
        scrollTop: $("#addComment").offset().top
    }, 2000); 
	//$('#editComment').fadeIn(1200);
}

function toggleEditComment(){
	tinyMCE.activeEditor.setContent('');
	$('#addComment').fadeOut(800);
	$('#editedCommentId').val(0);
	$('#ClickinguserId').val(0);
	$('#addCe1').hide();
	$('#addCe2').hide();
	$('#addC1').show();
	$('#addC2').show();
	$('#commentType').show();
}

function ajaxUpdateComment(){
	var newComment = tinyMCE.activeEditor.getContent();
	//alert('do zrobirnia!' + newComment);
	
	request = $.ajax({
    	url: "powerTrail/ajaxUpdateComment.php",
    	type: "post",
    	data:{text: newComment, dateTime: $('#commentDateTime').val(), ptId: $('#xmd34nfywr54').val(), commentId: $('#editedCommentId').val(), callingUser: $('#ClickinguserId').val()},
	});

    request.done(function (response, textStatus, jqXHR){
    	console.log(response);
    	ajaxGetComments(0, {commentsPaginateCount});
    	$('html, body').animate({
        	scrollTop: $("#ptComments").offset().top
    	}, 2000); 
    });
	
	request.always(function () {
		toggleEditComment();
    });
}

function deleteComment(commentId, callingUser){
	
	$("#dialog-form").dialog({
	autoOpen: false,
	height: 150,
	width: 350,
	modal: true,
	buttons: {
		"{{pt130}}": function() {
			if ($('#delReason').val() != ''){
				request = $.ajax({
				
		    	url: "powerTrail/ajaxRemoveComment.php",
		    	type: "post",
		    	data:{ptId: $('#xmd34nfywr54').val(), commentId: commentId, callingUser: callingUser, delReason: $('#delReason').val() },
				});
			
			    request.done(function (response, textStatus, jqXHR){
			    	console.log(response);
			    	if(response == 2) $("#commentType").append('<option selected="selected" value="2">{{pt065}}</option>');
			    });
			    
			    request.always(function () {
			    });
				
				ajaxGetComments(0, {commentsPaginateCount});
				$(this).dialog( "close" );
			}	
		},
		
		"{{pt031}}": function() {
			$(this).dialog( "close" );
		}
		},
	close: function() {
	}

	});
	
	$( "#dialog-form" ).dialog( "open" );
	$(".ui-dialog-titlebar-close").hide();	
	
}

function ajaxGetPtStats(){
	$("#ptStatsLoader").show();
	$('#ptStatsContainer').hide();
	$("#showPtStatsButton").fadeOut(500);
	
	
	request = $.ajax({
    	url: "powerTrail/ajaxPtStats.php",
    	type: "post",
    	data:{ptId: $('#xmd34nfywr54').val() },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$('#ptStatsContainer').html(response);
    	$('#ptStatsContainer').fadeIn(800);
    	$("#ptStatsOKimg").show();
    	$("#hidePtStatsButton").fadeIn(800);
    	$(function() {
			setTimeout(function() {
			   	$("#ptStatsOKimg").fadeOut(1200);
			}, 5000);
		});
    	// console.log(response);
    });
    
    request.always(function () {
    	$('#ptStatsLoader').hide();

    });
}

function ptStatsHide(){
	$('#ptStatsContainer').fadeOut(800);
	$("#hidePtStatsButton").fadeOut(800);
	$(function() {
		setTimeout(function() {
		   	$("#showPtStatsButton").fadeIn(800);
		}, 800);
	});
}

function ajaxUpdateName() {
	
	$('#nameAjaxLoader').show();
	// alert($('#ptName').val());
	request = $.ajax({
    	url: "powerTrail/ajaxUpdateName.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), newNamePt: $('#ptName').val() },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$('#powerTrailName').html(response);
    	$('#NameOKimg').show();
    	$(function() {
			setTimeout(function() {
			   	$("#NameOKimg").fadeOut(800);
			}, 800);
		});
    	console.log(response);
    });
    
    request.always(function () {
    	$('#nameAjaxLoader').hide();
    	toggleNameEdit();
    });
	
}

function toggleNameEdit() {
	if ($('#toggleNameEditButton').is(":visible")){
		$('#toggleNameEditButton').fadeOut(800);
		$(function() {
			setTimeout(function() {
			   	$("#editPtName").fadeIn(800);
			}, 800);
		});
	} else {
		$('#editPtName').fadeOut(800);
		$(function() {
			setTimeout(function() {
			   	$("#toggleNameEditButton").fadeIn(800);
			}, 800);
		});		
	}
}

function cancellEditName() {
	toggleNameEdit();
}
				
function cancellImage() {
	toggleImageEdit();
}				
				

function  ajaxGetPtCaches(getFinal){
	$('#cachesLoader').show();
	
	if(getFinal == 1){
		getFinal = '&choseFinalCaches=1'
	} else {
		getFinal = '';
	}
	
	request = $.ajax({
    	url: "powerTrail/ajaxGetPowerTrailCaches.php?ptAction=showSerie&ptrail="+$('#xmd34nfywr54').val()+getFinal,
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val()},
	});

    request.done(function (response, textStatus, jqXHR){
    	$('#PowerTrailCaches').html(response);
    });
    
    request.always(function () {
    	$('#cachesLoader').hide();
    });
}

function toggleStatusEdit() {
	if ($('#ptStatus').is(":visible")){
		$("#ptStatus").fadeOut(800);
		$("#ptStatusButton").fadeOut(800);
		$(function() {
			setTimeout(function() {
			   	$("#ptStatusEdit").fadeIn(800);
			}, 801);
		});
	} else {
		$("#ptStatusEdit").fadeOut(800);
		$(function() {
			setTimeout(function() {
			   	$("#ptStatus").fadeIn(800);
			   	$("#ptStatusButton").fadeIn(800);
			}, 801);
		});
	}
}

function ajaxUpdateStatus(){
	
	$('#ptStatusEdit').hide();
	$('#ajaxLoaderStatus').show();
	
	request = $.ajax({
    	url: "powerTrail/ajaxUpdateStatus.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(),  newStatus: $('#ptStatusSelector').val() },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	if(response != 'error'){
    		ajaxGetComments(0, {commentsPaginateCount});
    		toggleStatusEdit();
    		$('#StatusOKimg').show();
    		$(function() {
				setTimeout(function() {
   					$('#StatusOKimg').fadeOut(800); 
				}, 801);
			});
    		$('#ptStatus').html(response);
		}
    });
    
   request.fail(function (jqXHR, textStatus, errorThrown){
		toggleStatusEdit();
    });
    
    request.always(function () {
    	$('#ajaxLoaderStatus').hide();
    	
    });
}

function isNumberKey(evt) {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }

function startUpload(){
	$('#f1_upload_form').hide();
	$('#ajaxLoaderLogo').show();
    return true;
}

function stopUpload(success){
	// console.log(success);
    $('#ajaxLoaderLogo').hide();
    $('#powerTrailLogo').fadeOut(800);
    $(function() {
		setTimeout(function() {
			console.log('nowy obrzek to: '+success);
   			$('#powerTrailLogo').html(success); 
			$("#powerTrailLogo").fadeIn(800);
		}, 801);
	});
	toggleImageEdit()
	return true;   
}

function toggleImageEdit(){
	if ($('#toggleImageEditButton').is(":visible")){
		$("#toggleImageEditButton").fadeOut(800);
		$(function() {
			setTimeout(function() {
			   	$("#newImage").fadeIn(800);
			}, 801);
		});
	} else {
		$("#newImage").fadeOut(800);
		$(function() {
			setTimeout(function() {
			   	$("#toggleImageEditButton").fadeIn(800);
			   	$('#f1_upload_form').show();
			}, 801);
		});
	}
}


function ajaxUpdateDemandPercent() {
	$('#ajaxLoaderPercentDemand').show();
	$("#powerTrailpercentEdit").fadeOut(800);
	
	request = $.ajax({
    	url: "powerTrail/ajaxUpdateDemandPercent.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(),  newPercent: $('#demandPercent').val() },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	if(response != 'error'){
    		$('#powerTrailpercent').html($('#demandPercent').val());
    		$('#ptPercentOKimg').show;
    		$(function() {
				setTimeout(function() {
			   	$("#ptPercentOKimg").fadeOut(800);
			}, 801);
		});
		}
    });
    
    request.always(function () {
    	$("#powerTrailpercent").fadeIn(800);
		$('#percentDemandUserActions').fadeIn(800);
    	$('#ajaxLoaderPercentDemand').hide();
    });
}

function togglePercentSection() {
	if ($('#powerTrailpercent').is(":visible")){
		$("#powerTrailpercent").fadeOut(800);
		$('#percentDemandUserActions').fadeOut(800);
		$(function() {
			setTimeout(function() {
		    	$("#powerTrailpercentEdit").fadeIn(800);
		    }, 801);
		});
	} else {
		$("#powerTrailpercentEdit").fadeOut(800);
		$(function() {
			setTimeout(function() {
		    	$("#powerTrailpercent").fadeIn(800);
		    	$('#percentDemandUserActions').fadeIn(800);
		    }, 801);
		});
	}
	
}

function ajaxAddComment(){
	
	var newComment = tinyMCE.activeEditor.getContent();
	
	request = $.ajax({
    	url: "powerTrail/ajaxAddComment.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), text: newComment, type: $('#commentType').val(), datetime: $('#commentDateTime').val() },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	// $('#ptComments').html(response);
    	if($('#commentType').val() == 2){
			$("#commentType option[value='2']").remove();
    	}
    });
    request.always(function (response, textStatus, jqXHR) {
    	console.log(response);
    });
    
    if ($('#commentType').val() == 2) { // refresh conquest count
    	var newcount =  parseInt($('#conquestCount').html()) + 1;
    	$('#conquestCount').html(newcount);
    }
    toggleAddComment();
   
	$(function() {
		$('#ptComments').html('<img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />');
		setTimeout(function() {
	    	ajaxGetComments(0, {commentsPaginateCount});
	    	$('html, body').animate({
	        	scrollTop: $("#ptComments").offset().top
	    	}, 2000);   
	    }, 2000);

	});   
}

function toggleAddComment(){
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
	// alert(start+' '+limit);
	$('#ptComments').html('<br /><br /><center><img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" /><br /><br /></center>');
	request = $.ajax({
    	url: "powerTrail/ajaxGetComments.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), start: start, limit: limit },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	$('#ptComments').hide();
    	$('#ptComments').html(response);
    	$(function() {
		    $('#ptComments').fadeIn(800);
		});	
    	
        // console.log("Hooray, it worked!"+response);
    });
}

function toggleSearchCacheSection(){
	if ($('#toggleSearchCacheSection2').is(":visible")){
		$('#toggleSearchCacheSection2').fadeOut(800);
		$('#toggleSearchCacheSection0').fadeOut(800);
		$('#toggleSearchCacheSection3').fadeOut(800);
		$('#finalCachesbtn').fadeOut(800);
		$(function() {
			setTimeout(function() {
	       		$('#searchCacheSection').fadeIn(800);
	       		$('#toggleSearchCacheSection1').fadeIn(800);
	    	}, 801);
		});
	} else {
   		$('#searchCacheSection').fadeOut(800);
   		$('#toggleSearchCacheSection1').fadeOut(800);
   		$('#toggleSearchCacheSectionRm').fadeOut(800);
		$(function() {
			setTimeout(function() {
				$('#toggleSearchCacheSection2').fadeIn(800);
				$('#toggleSearchCacheSection0').fadeIn(800);
				$('#toggleSearchCacheSection3').fadeIn(800);
				$('#finalCachesbtn').fadeIn(800);
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

	request = $.ajax({
    	url: "powerTrail/ajaxAddCacheToPt.php",
    	type: "post",
    	data:{projectId: $('#xmd34nfywr54').val(), cacheId: newCacheId },
	});

    request.done(function (response, textStatus, jqXHR){
    	ajaxGetPtCaches();
    	if(response == 'cacheAdded') {
	    	$("#AloaderNewCacheAddingOKimg").fadeIn(800);
    		$('#message').html('{{pt162}}');
    		$("#messageDiv").fadeIn(800);
	    	$(function() {
		    	setTimeout(function() {
	       			$("#AloaderNewCacheAddingOKimg").fadeOut(1000);
	       			$("#messageDiv").fadeOut(1000);
	    		}, 3000);
			});
		}
		if(response == 'cache is already candidate or belongs to other pt') {
	    	$("#AloaderNewCacheAddingNOKimg").fadeIn(800);
    		$('#message').html('{{pt197}}');
    		$("#messageDiv").fadeIn(800);
	    	$(function() {
		    	setTimeout(function() {
	       			$("#AloaderNewCacheAddingNOKimg").fadeOut(1000);
	       			//$("#messageDiv").fadeOut(1000);
	    		}, 3000);
			});
		}
		if(response == 'cache added as cache candidate') {
	    	$("#AloaderNewCacheAddingIimg").fadeIn(800);
    		$('#message').html('{{pt198}}');
    		$("#messageDiv").fadeIn(800);
	    	$(function() {
		    	setTimeout(function() {
	       			$("#AloaderNewCacheAddingIimg").fadeOut(1000);
	       			// $("#messageDiv").fadeOut(1000);
	    		}, 3000);
			});
		}
		if(response == 'this cache cannot be removed') {
	    	$("#AloaderNewCacheAddingNOKimg").fadeIn(800);
    		$('#message').html('{{pt200}}');
    		$("#messageDiv").fadeIn(800);
	    	$(function() {
		    	setTimeout(function() {
	       			$("#AloaderNewCacheAddingNOKimg").fadeOut(1000);
	       			// $("#messageDiv").fadeOut(1000);
	    		}, 3000);
			});
		}
		
        console.log("ajaxAddOtherUserCache succesfully: "+response);
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    request.always(function () {
    	toggleSearchCacheSection();
    	$('#AloaderNewCacheAdding').hide();
    	
    });

	return false;
}

function checkCacheByWpt(id){
	$('#newCache2ptAddButton'+id).hide();
	$('#newCacheName'+id).html('');
	var waypoint = $('#CacheWaypoint'+id).val();
	if(waypoint.length >= 6) {
			var cacheName = ajaxRetreiveCacheName(waypoint, id);
	}
}

function ajaxRetreiveCacheName(waypoint, id) {

	$('#AloaderNewCacheSearch'+id).show();

	request = $.ajax({
    	url: "powerTrail/ajaxRetreiveCacheName.php",
    	type: "post",
    	data:{waypoint: waypoint },
	});

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
    	var cacheInfoArr = response.split('!1@$%3%7%4@#23557&^%%4#@2$LZA**&6545$###');
		$('#AloaderNewCacheSearch'+id).hide();
		$('#newCacheName'+id).html(cacheInfoArr[0]);
		$('#newCacheNameId'+id).val(cacheInfoArr[1]);
		if (cacheInfoArr[1] != ''){
			$('#newCache2ptAddButton'+id).fadeIn(500);
		}       
        // console.log("Hooray, it worked! "+response);
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
        console.log("ajaxUpdatType: "+response);
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
        console.log("ajaxUpdateDate: "+response);
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
        console.log("ajaxUpdatePtDescription: "+response);
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

    request.done(function (response, textStatus, jqXHR){
       	$('#powerTrailOwnerList').html(response); 
       	$('#ownerListOKimg').fadeIn(500);
       	$(function() {
	    	setTimeout(function() {
       			$("#ownerListOKimg").fadeOut(1000)
    		}, 3000);
		}); 
        // console.log("ajaxRemoveUserFromPt: "+response);
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
        console.error("The following error occured: "+ textStatus, errorThrown);
    });

    request.always(function () {
    	$('#ajaxLoaderOwnerList').hide();
    	$('#addUser').hide();
    	$('#ownerListUserActions').fadeIn(800);
    	$('.removeUserIcon').hide();
    	cancellAddNewUser2pt();
    });

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

    request.done(function (response, textStatus, jqXHR){
       	$('#ownerListOKimg').fadeIn(500);
       	$('#powerTrailOwnerList').html(response);
     	$(function() {
	    	setTimeout(function() {
       			$("#ownerListOKimg").fadeOut(1000)
    		}, 3000);
		}); 
        // console.log("ajaxAddNewUser2pt: "+response);
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
        console.error("The following error occured: "+textStatus, errorThrown);
    });

    request.always(function () {
    	$('#ajaxLoaderOwnerList').hide();
    	$('#addUser').fadeOut(800);
    	$('#ownerListUserActions').fadeIn(500);
    	$('.removeUserIcon').hide();
    	cancellAddNewUser2pt();
    });

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
        console.log("clickShow: "+response);
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
        console.error("The following error occured: "+ textStatus, errorThrown );
    });

    request.always(function () {
    	$('#ajaxLoaderCacheCount').hide();
    	$('#cacheCountUserActions').show();
    });

	return false;
	}

function ajaxAddCacheToPT(cacheId) {
	var projectId = $('#ptSelectorForCache'+cacheId).val();
    $('#addCacheLoader'+cacheId).show();
    $.ajax({
      url: "powerTrail/ajaxAddCacheToPt.php",
      type: "post",
      data: {projectId: projectId, cacheId: cacheId},
      success: function(data){
		if (data == 'cacheAddedToPt' || data == 'removed'){
          $("#h"+cacheId).val(projectId);
          $("#cacheInfo"+cacheId).show();
          $(function() {
	    	setTimeout(function() {
       			$("#cacheInfo"+cacheId).fadeOut(1000);
    		}, 3000);
		  });
		}
		
		if (data == 'this cache cannot be removed'){
			$("#cacheInfoNOK"+cacheId).show();
			var defVal = $("#h"+cacheId).val();
			$('#ptSelectorForCache'+cacheId).val(defVal);
			$(function() {
	    		setTimeout(function() {
       				$("#cacheInfoNOK"+cacheId).fadeOut(1000);
    			}, 3000);
			});
		}
		$('#addCacheLoader'+cacheId).hide();
	    console.log(data);
      },
      error:function(){
          alert("failure");
          $('#addCacheLoader'+cacheId).hide();
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

/* maps */

function initialize() {

if ({mapInit} == '0') {
	console.log('map is swithed off');
	return false;
}
console.log('initialize ');

var ptMapCenterLat = {mapCenterLat};
var ptMapCenterLon = {mapCenterLon};
var mapZoom = {mapZoom};
var fullCountryMap = {fullCountryMap};
var caches = [ {ptList4map} ];

//osm
var mapTypeIds = [];
for(var type in google.maps.MapTypeId) {
    mapTypeIds.push(google.maps.MapTypeId[type]);
}
mapTypeIds.push("OSM");
mapTypeIds.push("UMP");

//end osm

var myLatlng = new google.maps.LatLng(ptMapCenterLat, ptMapCenterLon);
var mapOptions = {
    zoom: mapZoom,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControlOptions: {
        mapTypeIds: mapTypeIds
    }
}
var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
var bounds = new google.maps.LatLngBounds(); 

map.mapTypes.set("OSM", new google.maps.ImageMapType({
                getTileUrl: function(coord, zoom) {
                    return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
                },
                tileSize: new google.maps.Size(256, 256),
                name: "OSM",
                maxZoom: 18
}));
map.mapTypes.set("UMP", new google.maps.ImageMapType({
                getTileUrl: function(coord, zoom) {
                    return "http://1.tiles.ump.waw.pl/ump_tiles/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
                },
                tileSize: new google.maps.Size(256, 256),
                name: "UMP",
                maxZoom: 18
}));

var infoWindow = new google.maps.InfoWindow;

var onMarkerClick = function() {
  var markerx = this;
  //var latLng = markerx.getTitle();
  infoWindow.setContent('<div class="mapCloud"><img src="'+this.ic+'"> ' + this.txt + '<div>');
  infoWindow.open(map, markerx);
};
google.maps.event.addListener(map, 'click', function() {
  infoWindow.close();
});



caches.forEach(function(cache) {
	  var marker = new google.maps.Marker({
      position: new google.maps.LatLng(cache[0],cache[1]),
      map: map,
	  icon: new google.maps.MarkerImage( cache[3], new google.maps.Size(18,21), new google.maps.Point(0,0),  new google.maps.Point(9, 21)),
      title: cache[4],
      txt: cache[2],
      ic: cache[3],
  });
  bounds.extend(marker.getPosition());
  
  google.maps.event.addListener(marker, 'click', onMarkerClick);
  
});
if(fullCountryMap == '0') map.fitBounds(bounds);
}

google.maps.event.addDomListener(window, 'load', initialize);
/* maps end */
// -->
</script>

<style>

.info {
	color: blue;
	font-size: 8px;
}

table, th, td
{
	font-size: 12px;
}

table.statsTable td{
	padding-left: 5px;
	padding-right: 5px;	
}
table.statsTable th{
	padding-left: 5px;
	padding-right: 5px;	
	border-bottom: solid 2px;
	border-color: #000044;
	background-color:#0088CC;
	color: #FFFFCC;

}
table.statsTable th:not(:last-child), table.statsTable td:not(:last-child){
	border-right: solid 1px;
}

table.ptCacheTable th{
	padding-left: 5px;
	padding-right: 5px;	
	border-bottom: solid 2px;
	background-color:#0088CC;
	color: #FFFFCC;
}


table.ptCacheTable th:first-child, table.statsTable th:first-child{
	-moz-border-top-left-radius: 5px;
	-webkit-border-top-left-radius: 5px;
	border-top-left-radius: 5px;
}
table.ptCacheTable th:last-child, table.statsTable th:last-child{
	-moz-border-top-right-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	border-top-right-radius: 5px;
}

#powerTrailName{
	font-size: 36px;
	color:#000088;
	font-family: Shojumaru;
}

.CommentDate {
	font-size: 11px;
	padding-left: 2px;
	padding-right: 15px;
}

.commentContent{
	border-left: 1px solid #2F2727;
	padding-left: 15px;
	padding-right: 20px;
	padding-top: 5px;
	padding-bottom: 5px;
	max-width:550px;
	height:auto;
}

.commentHead{
	padding-top: 5px;
	font-family: verdana;
	font-size: 13px;
	padding-left: 10px;
	background-color: #FFFFFF; background-repeat: repeat-y; 
	
	border-left: 1px solid #2F2727;
    border-top: 1px solid #2F2727;
	
	background: -webkit-gradient(linear, left top, right top, from(#DDDDDD), to(#FFFFFF)); 
	background: -webkit-linear-gradient(left, #DDDDDD  #FFFFFF); 
	background: -moz-linear-gradient(left, #DDDDDD, #FFFFFF); 
	background: -ms-linear-gradient(left, #DDDDDD, #FFFFFF); 
	background: -o-linear-gradient(left, #DDDDDD, #FFFFFF);
	
	-moz-border-top-left-radius: 10px;
	-webkit-border-top-left-radius: 10px;
	border-top-left-radius: 10px;
}

#commentsTable{
	width: 95%;
}

.linearBg1 {
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
.linearBg2 {
	height: 1px;
	padding-left:8px;
	background-color: #1a82f7; background-repeat: repeat-y; 
	background: -webkit-gradient(linear, left top, right top, from(#1a82f7), to(#2F2727)); 
	background: -webkit-linear-gradient(left, #2F2727, #1a82f7); 
	background: -moz-linear-gradient(left, #2F2727, #1a82f7); 
	background: -ms-linear-gradient(left, #2F2727, #1a82f7); 
	background: -o-linear-gradient(left, #2F2727, #1a82f7);
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

#toLowUserFound {
	padding-top: 50px;
	padding-left: 75px;
	font-size: 16px;
	color:#EE0000;
}

/* quite nice blue buttons */
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

.paginateButton {
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
	font-family:verdana;
	font-size:10px;
	font-weight:normal;
	padding:0px 2.5px;
	text-decoration:none !important;
	text-shadow:1px 1px 0px #1570cd;
}.paginateButton:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #1e62d0), color-stop(1, #3d94f6) );
	background:-moz-linear-gradient( center top, #1e62d0 5%, #3d94f6 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e62d0', endColorstr='#3d94f6');
	background-color:#1e62d0;
}.paginateButton:active {
	position:relative;
	top:1px;
}

.currentPaginateButton {
	-moz-box-shadow:inset 0px 1px 0px 0px #97c4fe;
	-webkit-box-shadow:inset 0px 1px 0px 0px #97c4fe;
	box-shadow:inset 0px 1px 0px 0px #97c4fe;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #dddddd), color-stop(1, #555555) );
	background:-moz-linear-gradient( center top, #bbbbbb 5%, #444444 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#aaaaaa', endColorstr='#333333');
	background-color:#000000;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #666666;
	display:inline-block;
	color:#ffffff !important;
	font-family:verdana;
	font-size:10px;
	font-weight:normal;
	padding:0px 2.5px;
	text-decoration:none !important;
	text-shadow:1px 1px 0px #1570cd;
}.currentPaginateButton:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #aaaaaa), color-stop(1, #222222) );
	background:-moz-linear-gradient( center top, #bbbbbb 5%, #444444 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999', endColorstr='#111111');
	background-color:#000000;
}.currentPaginateButton:active {
	position:relative;
	top:1px;
}

/* sexy tooltips */
.tooltip {
	border-bottom: 1px dotted #000000; color: #000000; outline: none;
	cursor: help; text-decoration: none;
	position: relative;
}
.tooltip span {
	margin-left: -999em;
	position: absolute;
}
.tooltip:hover span {
	border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; 
	box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 5px 5px rgba(0, 0, 0, 0.1); -moz-box-shadow: 5px 5px rgba(0, 0, 0, 0.1);
	font-family: Calibri, Tahoma, Geneva, sans-serif;
	position: absolute; left: 1em; top: 2em; z-index: 99;
	margin-left: 0; width: 250px;
}
.tooltip:hover img {
	border: 0; margin: -10px 0 0 -55px;
	float: left; position: absolute;
}
.tooltip:hover em {
	font-family: Candara, Tahoma, Geneva, sans-serif; font-size: 1.2em; font-weight: bold;
	display: block; padding: 0.2em 0 0.6em 0;
}
.classic { padding: 0.8em 1em; }
.custom { padding: 0.5em 0.8em 0.8em 2em; }
* html a:hover { background: transparent; }
.classic {background: #FFFFAA; border: 1px solid #FFAD33; }
.critical { background: #FFCCAA; border: 1px solid #FF3334;	}
.help { background: #9FDAEE; border: 1px solid #2BB0D7;	}
.info { background: #9FDAEE; border: 1px solid #2BB0D7;	}
.warning { background: #FFFFAA; border: 1px solid #FFAD33; }
/*sexy toltips end*/

#powerTrailDescription img {
	max-width:550px;
	height:auto;
}

#oldIE{
	color: red;
	border: solid 1px;
	border-color: red;
	padding: 10px;
	width:90%;
}

.editDeleteComment {
	float:right
}

#map-canvas, #map_canvas {
	margin-top: 20px;
	height: 460px;
	width: 500px;
	margin-left: auto;
	margin-right: auto;
	padding: 20px;
	border: 1px solid #0051B9
}
#mapOuterdiv{
	margin-left: auto;
	margin-right: auto;
}
#ptMenus{
	width: 99%;
	margin-left: auto;
	margin-right: auto;
}
.descTd{
	padding-left: 10px;
	padding-top: 3px;
}

.finalCache{
	color:#66CC00;
	font-size: 11px;
	float: right;
	font-weight: bold;
}

.mapCloud {
	padding-left: 10px;
	padding-right: 10px;
	padding-top: 5px;
	padding-bottom: 5px;
}

.searchCacheSection{
	border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; 
	border:1px solid #337fed;
	height: 80px;
	padding: 5px;
}
.padding1{
	padding: 1px;
}
.displayOptionsClass{
	border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; 
	border:1px solid #337fed;
	width: 80%;
	padding: 5px;
}
#messageDiv{
	display: none;
	border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; 
	border:1px solid #337fed;
	width: 80%;
	padding: 5px;
}
#gpxOptions{
	margin-left: auto;
	margin-right: auto;
	overflow: hidden;
	display: none;
	border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; 
	border:1px solid #337fed;
	width: 40%;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-left: 20px;
	padding-right: 20px;
}

</style>
<link rel="stylesheet" href="tpl/stdstyle/css/ptMenuCss/style.css" type="text/css" /><style type="text/css">._css3m{display:none}</style>

<body>

<input type="hidden" id="xmd34nfywr54" value="{powerTrailId}">

<br/><br/>
<!-- deleting entery comfirmation dialog  -->
<div id="dialog-form" title="{{pt151}}" style="display: none">
	<form>
	<label for="delReason">{{pt152}} (max. 500 {{pt154}})</label><br /><br />
	<input onkeypress="return event.keyCode != 13;" type="text" name="delReason" id="delReason" class="text ui-widget-content ui-corner-all" style="width: 280px;" maxlength="500" />
	</form>
</div>

<div id="oldIE" style="display: none">{{pt129}}</div>
	
<div class="content2-pagetitle"> 
 <img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="geocache" title="geocache" align="middle" /> 
 {{pt001}} 
</div> 

<!--[if IE 6 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]--> 
<!--[if IE 7 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]--> 
<!--[if IE 8 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]--> 

<div id="ptMenus" style="display: {ptMenu}">
<ul id="css3menu1" class="topmenu">
{powerTrailMenu}
</ul>
</div>
<!-- map -->
<div id="mapOuterdiv" style="display: {mapOuterdiv}">
<div id="map-canvas"></div>
</div>

<div style="display: {displayCreateNewPowerTrailForm}">
	<form name="createNewPowerTrail" id="createNewPowerTrail" action="powerTrail.php?ptAction=createNewPowerTrail" method="post">
		<table>
			<tr>
				<td>{{pt008}} </td>
				<td><input type="text" name="powerTrailName" id="fPowerTrailName" /></td>
			</tr>
			<tr>
				<td>
					{{pt009}}
					<a class="tooltip" href="javascript:void(0);">{{pt087}}?<span class="custom help"><img src="tpl/stdstyle/images/toltipsImages/Help.png" alt="Help" height="48" width="48" /><em>{{pt088}}</em>{{pt090}}</span></a>
				</td>
				<td>
					{ptTypeSelector}
				</td>
			</tr>
			<tr>
				<td>
					{{pt010}}
					<a class="tooltip" href="javascript:void(0);">{{pt087}}?<span class="custom help"><img src="tpl/stdstyle/images/toltipsImages/Help.png" alt="Help" height="48" width="48" /><em>{{pt088}}</em>{{pt089}}</span></a>
				</td>
				<td>		
					<select name="status">
						<option value="1">{{pt006}}</option>
						<option value="2">{{pt007}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					{{pt054}}
					<br /><a class="tooltip" href="javascript:void(0);">{{pt087}}?<span class="custom help"><img src="tpl/stdstyle/images/toltipsImages/Help.png" alt="Help" height="48" width="48" /><em>{{pt088}}</em>{{pt086}}</span></a>	
				</td>
				<td>		
					<input name="dPercent" onkeypress="return isNumberKey(event)" type="number" min="10" max="100" value="50" maxlength="3"/> 
				</td>
			</tr>
			<tr>
				<td>{{pt011}}</td>
				<td>		
					<textarea name="description"></textarea>
				</td>
			</tr>
		<tr>
			<td></td>
			<td>
				<input type="hidden" value="{{submit}}" name="createNewPowerTrailBtn" /><br />
				<a href="javascript:void(0);" onclick="$('#createNewPowerTrail').submit()"; class="editPtDataButton">{{pt080}}</a>
			</td>
		</tr>
		</table>
	</form>
</div>

<div style="display: {displayToLowUserFound}" id="toLowUserFound"><img src="tpl/stdstyle/images/toltipsImages/Critical.png" /> {{pt068}} {CFrequirment} {{pt069}} </div>

<div style="display: {displayUserCaches};">
	<div class="searchdiv">
		<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
		<tr>
		 <td>waypoint</td>
		 <td>{{cache_name}}</td>
		 <td>{{pt002}}</td>
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
<div style="display: {nocachess}">({{pt084}})</div>

<div style="display: {displayPowerTrails}">
	<table border=0 width=100%>
		<tr>
			<td colspan="8" class="linearBg1">{{pt035}}</td>
		</tr>
		<tr>
			<td colspan="8" align="center">
				<div class="displayOptionsClass" id="displayOptionsButton">
					{{pt175}}: {displayedPowerTrailsCount}
					<a href="javascript:void(0)" style="float: right;" onclick="showDisplayOptions()" class="editPtDataButton">{{pt163}}</a>
				</div>
				<div id="displayOptions" class="displayOptionsClass" style="display: none">
					<form id="dOptionForm" name="dOptionForm" action="powerTrail.php" method="get">
						<table>
							<tr>
								<td>{{pt166}}</td>
								<td>{ptTypeSelector2}</td>
							</tr>
							<tr>
								<td>{{pt167}}</td>
								<td>{sortSelector}</td>
							</tr>
							<tr>
								<td>{{pt178}}</td>
								<td>{sortDirSelector}</td>
							</tr>
						</table>
						<a href="javascript:void(0)" id="confirmDisplayOptionsButton" onclick="document.dOptionForm.submit();" class="editPtDataButton">{{pt164}}</a>
						<a href="javascript:void(0)" id="displayOptionsButton" onclick="showDisplayOptions()" class="editPtDataButton">{{pt031}}</a>
					</form>
				</div>
			</td>
		</tr>
		<tr>
			<th class="ptTd">{{pt036}}</th>
			<th class="ptTd">{{pt039}}</th>
			<th class="ptTd">{statusOrPoints}</th>
			<th class="ptTd">{{pt041}}</th>
			<th class="ptTd">{{pt042}}</th>
			<th class="ptTd">{{pt057}}</th>
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
			<td width=251>
				<table style="height: 250px; width: 250px;"><tr><td valign="center" align="center"><span id="powerTrailLogo"><img src="{powerTrailLogo}" /></span></td></tr></table>
				<img style="display: none" id="ajaxLoaderLogo" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
			</td>
			<td align="center">
				<span id="powerTrailName">{powerTrailName}</span> <img id="NameOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" /> <!-- [ ? TU WSTAWIĆ MAPĘ ? ] -->
			</td>
		</tr>
		<tr>
			<td>
				<p align="center" id="toggleImageEditButton" style="display: {displayAddCachesButtons}">
					<a href="javascript:void(0)" onclick="toggleImageEdit()" class="editPtDataButton">{{pt060}}</a>
				</p>
				<span id="newImage" style="display: none"> 
					<form action="powerTrail/ajaxImage.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUpload();" >
         				<p id="f1_upload_form" align="center"><br/>
             			File: <input name="myfile" type="file" size="30" />
             			<input type="hidden" name="powerTrailId" value="{powerTrailId}">
						<a href="javascript:void(0)" onclick="cancellImage()" class="editPtDataButton">{{pt031}}</a>
         				<a href="javascript:void(0)" onclick="$(this).closest('form').submit()" class="editPtDataButton">{{pt044}}</a> 
         				</p>
         				<iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
				</span>
			</td>
			<td>
				<p align="center">
				<span id="toggleNameEditButton" style="display: {displayAddCachesButtons}">
					<a href="javascript:void(0)" onclick="toggleNameEdit()" class="editPtDataButton">{{pt091}}</a>
				</span>
				<img id="nameAjaxLoader" style="display: none" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
				<span id="editPtName" style="display: none">
					<input type="text" id="ptName" value="{powerTrailName}" />
					<a href="javascript:void(0)" onclick="cancellEditName()" class="editPtDataButton">{{pt031}}</a>
					<a href="javascript:void(0)" onclick="ajaxUpdateName()" class="editPtDataButton">{{pt044}}</a>
				</span>
				</p>	
			</td>
		</tr>
		<tr>
			<td colspan="3" class="linearBg1">{{pt019}}</td>
		</tr>
		<tr>
			<td class="descTd">{{pt181}} <a class="tooltip" href="javascript:void(0);"><i style="color: blue;">(?)</i><span class="custom help"><img src="tpl/stdstyle/images/toltipsImages/Help.png" alt="Help" height="48" width="48" /><em>{{pt181}}</em>{{pt182}}</span></a></td>
			<td><a href="viewprofile.php?userid={leadingUserId}">{leadingUserName}</a>
			</td>
		</tr>
		<tr>
			<td class="descTd"><div style="display: {displayAddCachesButtons}">{{pt063}}</div></td>
			<td>
				<span id="ptStatus" style="display: {displayAddCachesButtons}">
					{ptStatus}
				</span>
				<img id="StatusOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
				<span id="ptStatusEdit" style="display: none">
					{ptStatusSelector}
					<a href="javascript:void(0)" onclick="toggleStatusEdit();" class="editPtDataButton">{{pt031}}</a>	
					<a href="javascript:void(0)" onclick="ajaxUpdateStatus();" class="editPtDataButton">{{pt044}}</a>	
				</span>	
			</td>
			<td align="right" width="120">
				<a href="javascript:void(0)" style="display: {displayAddCachesButtons}" id="ptStatusButton" onclick="toggleStatusEdit()" class="editPtDataButton">{{pt064}}</a>	
				<span style="display: none" id="ajaxLoaderStatus"><img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" /></span>
			</td>
		</tr>
		<tr>
			<td class="descTd">{{pt065}}</td>
			<td><span id="conquestCount">{conquestCount}</span> {{pt066}}</td>
		</tr>
		<tr>
			<td class="descTd">{{pt037}}</td>
			<td><span id="conquestCount">{ptPoints}</span> {{pt038}}</td>
		</tr>
		<tr>
			<td class="descTd">{{pt022}}</td>
			<td><span id="powerTrailCacheCount">{powerTrailCacheCount}</span><img id="cCountOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" /></td>
			<td align="right">
				<span class="userActions" id="cacheCountUserActions">{cacheCountUserActions}</span>
				<span style="display: none" id="ajaxLoaderCacheCount"><img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" /></span>
			</td>
		</tr>
		<tr>
			<td class="descTd">{{pt054}}</td>
			<td>
				<span id="powerTrailpercent">{powerTrailDemandPercent}</span>% <img id="percentCountOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
				<span id="powerTrailpercentEdit" style="display: none">
					<input id="demandPercent" onkeypress="return isNumberKey(event);" onkeyup="updateCc();" onchange="updateCc();" type="number" min="10" max="100" value="{powerTrailDemandPercent}" maxlength="3"/>
					<a href="javascript:void(0)" onclick="togglePercentSection(); $('#ptPercentCc').html(calculatepercentCc( $('#powerTrailpercent').html(), $('#powerTrailCacheCount').html()));" class="editPtDataButton">{{pt031}}</a>	
					<a href="javascript:void(0)" onclick="ajaxUpdateDemandPercent()" class="editPtDataButton">{{pt044}}</a>	
				</span>
				(<span id="ptPercentCc"></span> {{pt180}})
				<img id="ptPercentOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
			</td>
			<td align="right">
				<span class="userActions" id="percentDemandUserActions" style="display: {percentDemandUserActions}">
					<a href="javascript:void(0)" onclick="togglePercentSection()" class="editPtDataButton">{{pt055}}</a>	
				</span>
				<span style="display: none" id="ajaxLoaderPercentDemand"><img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" /></span>
			</td>
		</tr>
		<tr>
			<td class="descTd">{{pt023}}</td>
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
			<td class="descTd">{{pt024}}</td>
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
			<td class="descTd">{{pt025}}</td>
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
			<td colspan="3" class="linearBg1">{{pt034}}</td>
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
			<td class="linearBg1">{{pt020}} {powerTrailName}</td>
		</tr>
		<tr>
			<td>
				<div id="gpxOptions">	
					<p>{{pt206}}:</p><hr>	
					<input id="f_inactive"  type="checkbox" name="f_inactive"  checked="checked" /> {{pt201}} <br/>
					<input id="f_ignored"   type="checkbox" name="f_ignored"   checked="checked" /> {{pt202}} <br/>
					<input id="f_userowner" type="checkbox" name="f_userowner" checked="checked" /> {{pt203}} <br/>
					<input id="f_watched"   type="checkbox" name="f_watched"   checked="checked" /> {{pt204}} <br/>
					<input id="f_userfound" type="checkbox" name="f_userfound" checked="checked" /> {{pt205}} <br/>
					<input id="showresult"  type="hidden"   name="showresult" value="1">
					<input id="count"       type="hidden"   name="count"      value="max">
					<input id="ptId"        type="hidden"   name="ptId"       value="{powerTrailId}">
					<input id="output" 	  	type="hidden"   name="output" 	  value="gpxgc">
					<a href="javascript:void(0)" style="float: right;" onclick="$('#gpxOptions').fadeOut(600);$(function(){setTimeout(function(){$('#gpxSection').fadeIn(800);},801);});" class="editPtDataButton"/>OK</a>
				</div>
				
				<div id="gpxSection" align="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 5px; width=100%;">
					<a href="javascript:void(0)" style="float: left;" onclick="$('#gpxSection').fadeOut(800);$(function(){setTimeout(function(){$('#gpxOptions').fadeIn(800);},801);});" class="editPtDataButton"/>{{pt207}}</a>
					{{pt179}}: 
					<a href="javascript:void(0)" onclick="getGpx('gpxgc');" class="editPtDataButton">GPX</a>&nbsp;<a href="javascript:void(0)" onclick="getGpx('zip');" class="editPtDataButton">GARMIN ({{format_pict}})</a>
				</div>
			</td>
		</tr>
		<tr>
			<td class="linearBg2"></td>
		</tr>
	</table>
	
	<span id="PowerTrailCaches"></span>
	<img id="cachesLoader" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />

	<table border="0" width="90%" align="center">
	<tr>
		<td colspan="2">
			<div id="searchCacheSection" class="searchCacheSection" style="display: none">
				{{pt157}}:<br /><br />
				<input onkeyup="checkCacheByWpt('')" size="6" id="CacheWaypoint" type="text" maxlength="6" value="{ocWaypoint}" />
				<img style="display: none" id="AloaderNewCacheSearch" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
				<span id="newCacheName"></span>
				<input type="hidden" id="newCacheNameId" value="-1">
				<br/><br/><a href="javascript:void(0)" id="newCache2ptAddButton" style="display: none" onclick="ajaxAddOtherUserCache()" class="editPtDataButton">{{pt047}}</a>
				<a href="javascript:void(0)" id="toggleSearchCacheSection1" style="display: none" onclick="toggleSearchCacheSection()" class="editPtDataButton">{{pt031}}</a>
			</div>
			
			<div id="searchCacheSectionRm" class="searchCacheSection" style="display: none">
				{{pt158}}:<br /><br />
				<input onkeyup="checkCacheByWpt(2)" size="6" id="CacheWaypoint2" type="text" maxlength="6" value="{ocWaypoint}" />
				<img style="display: none" id="AloaderNewCacheSearch2" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
				<span id="newCacheName2"></span>
				<input type="hidden" id="newCacheNameId2" value="-1">
				<br/><br/><a href="javascript:void(0)" id="newCache2ptAddButton2" style="display: none" onclick="ajaxRmOtherUserCache()" class="editPtDataButton">{{pt159}}</a>
				<a href="javascript:void(0)" id="toggleSearchCacheSectionRm" style="display: none" onclick="toggleSearchCacheSection2()" class="editPtDataButton">{{pt031}}</a>
			</div>
			
			<img style="display: none" id="AloaderNewCacheAdding" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
			<img id="AloaderNewCacheAddingOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
			
			
			<div id="messageDiv">
				<table border="0">
					<tr>
						<td rowspan="2">
							<img id="AloaderNewCacheAddingNOKimg" style="display: none" src="tpl/stdstyle/images/log/16x16-dnf.png" />
							<img id="AloaderNewCacheAddingIimg" style="display: none" src="tpl/stdstyle/images/misc/16x16-info.png" />
						</td>
						<td>
							{{pt199}}
							<img id="closeMessage" onclick="$('#messageDiv').fadeOut(600);" style="float:right;" src="tpl/stdstyle/images/free_icons/cross.png" />
						</td>
					</tr>
					<tr>
						<td>
							<span id="message"></span>
						</td>
					</tr>
				</table>

			</div>
		</td>
		<td align="right">
			<div style="display: {displayAddCachesButtons}">
				<div class="padding1"><a href="powerTrail.php?ptAction=selectCaches" id="toggleSearchCacheSection0" class="editPtDataButton">{{pt049}}</a></div>
				<div class="padding1"><a href="javascript:void(0)" id="finalCachesbtn" onclick="reloadWithFinalsChoice();" class="editPtDataButton">{{pt149}}</a></div>
				<div class="padding1"><a href="javascript:void(0)" id="toggleSearchCacheSection2" onclick="toggleSearchCacheSection()" class="editPtDataButton">{{pt048}}</a><span id="removeCacheButton" style="display: {removeCacheButtonDisplay};">&nbsp;<a href="javascript:void(0)" id="toggleSearchCacheSection3" onclick="toggleSearchCacheSection2()" class="editPtDataButton">{{pt156}}</a></span></div>
			</div>
		</td>
	</tr>
	</table>
	
	<table border=0 width=100%>
		<tr>
			<td class="linearBg1">{{pt099}} {powerTrailName}</td>
		</tr>
		<tr>
			<td>
				{{pt015}}: <br />
				<p align="center"><img src="http://chart.apis.google.com/chart?cht=p3&chd=t:{cacheFound},{powerTrailCacheLeft}&chco=00AA00|0000AA&chs=300x120&chl={{pt103}}|{{pt104}}" /><br />
				{powerTrailserStats}</p>
			</td>
		</tr>
		<tr>
			<td>
				<div id="ptStatsContainer"></div>
				<a href="javascript:void(0)" id="showPtStatsButton" onclick="ajaxGetPtStats()" class="editPtDataButton">{{pt098}}</a>
				<img id="ptStatsLoader" style="display: none" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
				<img id="ptStatsOKimg" style="display: none" src="tpl/stdstyle/images/free_icons/accept.png" />
				<br /><br />
				<a href="javascript:void(0)" id="hidePtStatsButton" onclick="ptStatsHide()" class="editPtDataButton" style="display: none">{{pt100}}</a>
			</td>
		</tr>
	</table>
	
	<!-- power Trail comments -->
	<table border=0 width=100%>
		<tr>
			<td class="linearBg1">{{pt050}}</td>
		</tr>
	</table>


	<span id="ptComments">
	<img id="commentsLoader" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
	</span>
	<div id="animateHere"></div>
	<p style="display: {displayAddCommentSection}" align="right"><a href="javascript:void(0)" id="toggleAddComment" onclick="toggleAddComment()" class="editPtDataButton">{{pt051}}</a>&nbsp; </p>
	<div id="addComment" style="display: none">
		<input type="hidden" id="editedCommentId" value="0" />
		<input type="hidden" id="ClickinguserId" value="0" />
		<textarea id="addCommentTxtArea"></textarea>
		{ptCommentsSelector}
		<br />
		<input type="text" id="commentDateTime" value="{date}">
		<br /><br />
		<a id="addC1" href="javascript:void(0)" onclick="toggleAddComment();" class="editPtDataButton">{{pt031}}</a>
		<a id="addC2" href="javascript:void(0)" onclick="ajaxAddComment();" class="editPtDataButton">{{pt044}}</a>
		<a id="addCe1" href="javascript:void(0)" onclick="toggleEditComment();" class="editPtDataButton" style="display: none" >{{pt031}}</a>
		<a id="addCe2" href="javascript:void(0)" onclick="ajaxUpdateComment();" class="editPtDataButton" style="display: none">{{pt044}}</a>
		<br /><br />
	</div>
	
</div>