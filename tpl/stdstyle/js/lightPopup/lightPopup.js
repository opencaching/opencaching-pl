/**
 * This function displays div with given id as popup.
 * Element shows top-left corner of the popup.
 *
 * @param element
 * @param popupId
 * @returns
 */
function showLightPopup(element,popupId){

  var popup = $('#'+popupId);
  popupVisible = popup.hasClass('lightPopupVisible');

  // hide all visible popups
  $('.lightPopupVisible').toggleClass('lightPopupVisible lightPopupHidden');

  // find positions of el.
  var parentPosition = $(element).offset();

  if(!popupVisible){
    popup.toggleClass('lightPopupVisible lightPopupHidden');
  }

  var popupLeft = (parentPosition.left + $(document).width()/2)/2
  var popupTop = parentPosition.top;

  // set same position
  popup.css({top: popupTop, left: popupLeft});

}

