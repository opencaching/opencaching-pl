

/* totalStats right arrow  */
function countersLeft(){
  // hide left element
  $("#totalStatsDiv .counterWidget:not(.counterLeftHidden):first").toggleClass('counterLeftHidden');

  if( $("#totalStatsDiv .counterRightHidden:first").length == 0 ){
    // move counter from left to right
    el = $("#totalStatsDiv .counterLeftHidden:first");
    el.appendTo($("#totalStatsCounters"));
    el.toggleClass('counterLeftHidden');
    el.toggleClass('counterRightHidden');
  }
  $("#totalStatsDiv .counterRightHidden:first").toggleClass('counterRightHidden');
}

/* totalStats left arrow  */
function countersRight(){
  // hide left element
  $("#totalStatsDiv .counterWidget:not(.counterRightHidden):last").toggleClass('counterRightHidden');

  if( $("#totalStatsDiv .counterLeftHidden:last").length == 0 ){
    // move counter from right to left
    el = $("#totalStatsDiv .counterRightHidden:last");
    el.prependTo($("#totalStatsCounters"));
    el.toggleClass('counterLeftHidden');
    el.toggleClass('counterRightHidden');
  }
  $("#totalStatsDiv .counterLeftHidden:last").toggleClass('counterLeftHidden');
}

