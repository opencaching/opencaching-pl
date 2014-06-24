
function ToChangeLogRating( logid, target, cacheid) {

var positionY = document.body.scrollTop+0;

if ( positionY == 0)
    positionY = document.documentElement.scrollTop;

if ( positionY == 0)
    positionY = window.pageYOffset;

window.location.href = 'changelograting.php?logid='+logid.toString()+'&target='+target+'&cacheid='+cacheid.toString()+'&posY='+positionY.toString();

}
