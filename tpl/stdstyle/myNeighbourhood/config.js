// Slider to choose MyNbh caches item
$(function() {
	var handle = $("#nbh-custom-handle");
	$("#nbh-slider").slider({
		value : cachesCount,
		min : minCaches,
		max : maxCaches,
		create : function() {
			handle.text($(this).slider("value"));
		},
		slide : function(event, ui) {
			handle.text(ui.value);
			$("#input-caches").val(ui.value);
		}
	});
});

// Buttons to choose MyNbh style
$(function() {
	$(".nbh-radio").checkboxradio();
	$("fieldset").controlgroup();
});

// Delete Nbh button
$("#nbh-delete-btn").click(function(e) {
	e.preventDefault();
	$("#nbh-delete-dialog-confirm").dialog("open");
});

// Delete Nbh - confirm dialog
$(function() {
	$("#nbh-delete-dialog-confirm").dialog({
		autoOpen : false,
		resizable : false,
		modal : true,
		hide : 250,
		closeText : cancelButton,
		buttons : [ {
			text : deleteButton,
			click : function() {
				window.location = deleteLink;
			},
		}, {
			text : cancelButton,
			click : function() {
				$(this).dialog("close");
			}
		} ]
	});
});
