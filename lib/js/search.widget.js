/**
 * this is used by search widget
 */
function chname(newName, searchPage) {
	document.getElementById("search_input").name = newName;
	document.getElementById("search_form").action = searchPage;
	return false;
}