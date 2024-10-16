$(".nbh-sort-list").sortable({
    handle : ".nbh-block-header",
    opacity : 0.7,
    placeholder : "nbh-block-placeholder",
    update : function(event, ui) {
        let postData = $(this).sortable("serialize");

        if (InteractiveMapServices.getInteractiveMap("nbhmap")) {
            // get current order of sections
            var orders = {};
            var n = 0;
            $(this).sortable("toArray").forEach(function(v) {
                orders[v.replace('item_', '')] = n++;
            });
            // reorder map sections to move the highest section features to front
            InteractiveMapServices.reorderSections("nbhmap", orders);
        }

        $.ajax({
            url : changeOrderUri,
            type : "post",
            data : {
                order : postData
            }
        });
    }
});

$(".nbh-hide-toggle").on("click", function() {
    let icon = $(this);
    icon.closest(".nbh-block").find(".nbh-block-content").toggleClass(
        'nbh-nodisplay');
    let hidden = icon.closest(".nbh-block").find(".nbh-block-content")
        .hasClass("nbh-nodisplay");
    let itemId = icon.closest(".nbh-block").attr('id');
    let section = icon.closest(".nbh-block").attr('section');
    $.ajax({
        url : changeDisplayUri,
        type : "post",
        data : {
            hide : hidden,
            item : itemId
        }
    })
    if (InteractiveMapServices.getInteractiveMap("nbhmap")) {
        if (this.id == "nbh-map-hide") {
            InteractiveMapServices.getMapObject("nbhmap").updateSize();
        } else {
            InteractiveMapServices.toggleSectionVisibility("nbhmap", section);
        }
    }
});

$(".nbh-size-toggle").on("click", function() {
	let icon = $(this);
	icon.closest(".nbh-block").toggleClass("nbh-half nbh-full");
	let sizeClass = icon.closest(".nbh-block").hasClass("nbh-full");
	let itemId = icon.closest(".nbh-block").attr('id');
	$.ajax({
		url : changeSizeUri,
		type : "post",
		data : {
			size : sizeClass,
			item : itemId
		}
	});
	if (this.id == "nbh-map-resize") {
        var map = InteractiveMapServices.getMapObject("nbhmap");
        if (map) {
            map.updateSize();
        }
	}
});

$("div[id^='mynbh_item_'").mouseenter(function() {
    var id = this.id.substring("mynbh_item_".length);
    var parts = id.split('_');
    InteractiveMapServices.highlightFeature("nbhmap", parts[1], parts[2], parts[0]);
});

$("div[id^='mynbh_item_'").mouseleave(function() {
    InteractiveMapServices.toneDownFeature("nbhmap");
});
