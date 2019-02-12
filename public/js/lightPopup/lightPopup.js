/**
 * This function displays div with given id as popup.
 * Element shows top-left corner of the popup.
 *
 * @param element
 * @param popupId
 * @returns
 */
function showLightPopup(parentElement,popupId){

  var popup = $('#'+popupId);
  popupVisible = popup.hasClass('lightPopupVisible');

  // hide all visible popups
  $('.lightPopupVisible').toggleClass('lightPopupVisible lightPopupHidden');

  if(!popupVisible){ // popup is not visible right now - so let's show it
    // set same position as parent
    popup.toggleClass('lightPopupVisible lightPopupHidden');
    popup.offset($(parentElement).offset());
  }
}

