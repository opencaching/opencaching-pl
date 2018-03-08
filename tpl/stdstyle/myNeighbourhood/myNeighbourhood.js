$(".nbh-sort-list").sortable({
	handle : ".nbh-block-header",
	opacity : 0.7,
	placeholder : "nbh-block-placeholder",
	update : function(event, ui) {
		let postData = $(this).sortable("serialize");
		$.ajax({
			url : changeOrderUri,
			type : "post",
			data : {
				order : postData
			}
		});
	}
});

$(".nbh-hide-toggle").on(
		"click",
		function() {
			let icon = $(this);
			icon.closest(".nbh-block").find(".nbh-block-content").toggleClass(
					'nbh-nodisplay');
			let hidden = icon.closest(".nbh-block").find(".nbh-block-content")
					.hasClass("nbh-nodisplay");
			let itemId = icon.closest(".nbh-block").attr('id');
			$.ajax({
				url : changeDisplayAjaxUri,
				type : "post",
				data : {
					hide : hidden,
					item : itemId
				}
			})
			if (this.id == "nbh-map-hide") {
				google.maps.event.trigger(dynamicMapParams_nbhmap.map, "resize");
	}
		});

$(".nbh-size-toggle").on("click", function() {
	let icon = $(this);
	icon.closest(".nbh-block").toggleClass("nbh-half nbh-full");
	let sizeClass = icon.closest(".nbh-block").hasClass("nbh-full");
	let itemId = icon.closest(".nbh-block").attr('id');
	$.ajax({
		url : changeSizeAjaxUri,
		type : "post",
		data : {
			size : sizeClass,
			item : itemId
		}
	});
	if (this.id == "nbh-map-resize") {
		google.maps.event.trigger(dynamicMapParams_nbhmap.map, "resize");
	}
});
