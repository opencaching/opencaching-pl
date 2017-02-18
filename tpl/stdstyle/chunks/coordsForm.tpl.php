<?php
use lib\Objects\Coordinates\Coordinates;

/**
 * This chunk is purposed to be uniform coordinates edit form
 * There are many places when coords needs to be inserted (viewcache, editcache, Geopath edit etc..)
 * and one chunk could be used everywhere...
 *
 * Don't read separate fields - final coords are always stored in hidden inputs with ids/names:
 *
 * - <$inputPrefix>FinalLatitude
 * - <$inputPrefix>FinalLongitude
 *
 * Additionally, to handle if coords are ready check the value of hidden input with id: <$inputPrefix>FinalCoordsReady.
 * Also note thet OnChange event is triggered on this input when its value is changed
 *
 */

return function (Coordinates $initCoords = null, $inputPrefix='') {

    $prefix=(empty($inputPrefix))?'coordsChunk_':$inputPrefix;

    $finalLatId = $inputPrefix.'FinalLatitude';
    $finalLonId = $inputPrefix.'FinalLongitude';
    $finalCoordsReadyId =  $inputPrefix.'FinalCoordsReady';


    //TODO: read it from config...
    $defaultLat = 'N';
    $defaultLon = 'E';

    $f = Coordinates::COORDINATES_FORMAT_DEG_MIN;

    list($latLetter, $latDeg, $latMin) = ($initCoords)?$initCoords->getLatitudeParts($f):array($defaultLat,'','');
    list($lonLetter, $lonDeg, $lonMin) = ($initCoords)?$initCoords->getLongitudeParts($f):array($defaultLon,'','');

    $selectN = ($latLetter == 'N')?'selected="selected"':'';
    $selectS = ($latLetter == 'S')?'selected="selected"':'';
    $selectE = ($lonLetter == 'E')?'selected="selected"':'';
    $selectW = ($lonLetter == 'W')?'selected="selected"':'';

?>
<script src="lib/js/jquery.mask.min.js"></script>
<script type="text/javascript">

  function <?=$prefix?>updateCoords(){
    // recalculate current values

    var latDeg = parseInt( $('#<?=$prefix?>DegNs').val());
    var latMin = parseFloat( $('#<?=$prefix?>MinNs').val());
    var lonDeg = parseInt( $('#<?=$prefix?>DegWe').val());
    var lonMin = parseFloat( $('#<?=$prefix?>MinWe').val());

    if( isNaN(latDeg) || isNaN(latMin)  || isNaN(lonDeg) || isNaN(lonMin) ){
      //one of coords-part is not ready
      $('#<?=$finalLatId?>').val('');
      $('#<?=$finalLonId?>').val('');

      $('#<?=$finalCoordsReadyId?>').val('').trigger('change');
    }else{

        var lat = parseInt( $('#<?=$prefix?>SelectNs').val()) * (latDeg + (latMin/60));
        var lon = parseInt( $('#<?=$prefix?>SelectWe').val()) * (lonDeg + (lonMin/60));

        $('#<?=$finalLatId?>').val(lat);
        $('#<?=$finalLonId?>').val(lon);

        $('#<?=$finalCoordsReadyId?>').val(1).trigger('change');

    }
  }

  $(document).ready(function(){


    $('#<?=$prefix?>DegNs').mask('00', {
        placeholder: "??",
        onChange: <?=$prefix?>updateCoords
    });

    $('#<?=$prefix?>MinNs').mask('50.000', {
        placeholder: "??.???",
        translation: {
          '5': {
            pattern: /[0-5]/, optional: true
          }
        },
        onChange: <?=$prefix?>updateCoords
    });

    $('#<?=$prefix?>DegWe').mask('B00', {
        placeholder: "???",
        translation: {
          'B': {
            pattern: /[0-1]/, optional: true
          }
        },
        onChange: <?=$prefix?>updateCoords
    });

    $('#<?=$prefix?>MinWe').mask('50.000', {
        placeholder: "??.???",
        translation: {
        '5': {
          pattern: /[0-5]/, optional: true
        }
        },
        onChange: <?=$prefix?>updateCoords
    });

    $('#<?=$prefix?>SelectNs').change(<?=$prefix?>updateCoords);
    $('#<?=$prefix?>SelectWe').change(<?=$prefix?>updateCoords);

    <?=$prefix?>updateCoords();

  });
</script>
<style type="text/css">
  .<?=$prefix?>bigFont { font-size: 1.5em; margin-left: 3px; }
</style>

<!-- Final coords -->
<input id="<?=$finalLatId?>" name="<?=$finalLatId?>" type="text" value="" />
<input id="<?=$finalLonId?>" name="<?=$finalLonId?>" type="text" value="" />
<input id="<?=$finalCoordsReadyId?>" type="hidden" value="false" />

<fieldset style="border: 1px solid black; background-color: #FAFBDF; width: 200px; " class="form-group-sm">

  <legend><strong>WGS-84</strong></legend>

  <div>
      <select id="<?=$prefix?>SelectNs" class="form-control input50">
        <option <?=$selectN?> value="1">N</option>
        <option <?=$selectS?> value="-1">S</option>
      </select>

      <input id="<?=$prefix?>DegNs" class="form-control input40" type="text" value="<?=$latDeg?>"/>
      <span class="<?=$prefix?>bigFont">&deg;</span>
      <input id="<?=$prefix?>MinNs" class="form-control input50" type="text" value="<?=$latMin?>"/>
      <span class="<?=$prefix?>bigFont">'</span>

  </div>

  <div>
      <select id="<?=$prefix?>SelectWe" class="form-control input50">
        <option <?=$selectE?> value="1">E</option>
        <option <?=$selectW?> value="-1">W</option>
      </select>

      <input id="<?=$prefix?>DegWe" class="form-control input40" type="text" value="<?=$lonDeg?>"/>
      <span class="<?=$prefix?>bigFont">&deg;</span>
      <input id="<?=$prefix?>MinWe" class="form-control input50" type="text" value="<?=$lonMin?>" />
      <span class="<?=$prefix?>bigFont">'</span>

  </div>
</fieldset>

<?php
};

// end of chunk - nothing should be added below
