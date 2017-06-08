
<div class="content2-pagetitle">
  <b>{{rating_title}}</b>
</div>

<p>{{rating_invitation}}</p>

<form action='' method="post" id="rating-form">

<fieldset>
  <legend>{{rating_equipment}}</legend>

  <p>({{rating_equip_desc}})</p>

  <input type="radio" id="Equipment0" name="Equipment" value="0" checked="checked">
  <label for="Equipment0">{{rating_no}}</label>

  <input type="radio" id="Equipment1" name="Equipment" value="4">
  <label for="Equipment1">{{rating_yes}}</label>
</fieldset>


<fieldset>
  <legend>{{rating_overnight}}</legend>

  <input type="radio" id="Night0" name="Night" value="0" checked="checked">
  <label for="Night0">{{rating_no}}</label>

  <input type="radio" id="Night3" name="Night" value="3">
  <label for="Night3">{{rating_yes}}</label>
</fieldset>


<fieldset>
  <legend>{{rating_length}}</legend>
  <p>({{rating_len_desc}})</p>

  <input type="radio" id="Length0" name="Length" value="0" checked="checked">
  <label for="Length0">{{rating_length0}}</label>

  <input type="radio" id="Length1" name="Length" value="1">
  <label for="Length1">{{rating_length1}}</label>

  <input type="radio" id="Length2" name="Length" value="2">
  <label for="Length2">{{rating_length2}}</label>

  <input type="radio" id="Length3" name="Length" value="3">
  <label for="Length3">{{rating_length3}}</label>
</fieldset>


<fieldset>
  <legend>{{rating_trail}}</legend>

  <input type="radio" id="Trail0" name="Trail" value="0" checked="checked">
  <label for="Trail0">{{rating_trail0}}</label>

  <input type="radio" id="Trail1" name="Trail" value="1">
  <label for="Trail1">{{rating_trail1}}</label>

  <input type="radio" id="Trail2" name="Trail" value="2">
  <label for="Trail2">{{rating_trail2}}</label>

  <input type="radio" id="Trail3" name="Trail" value="3">
  <label for="Trail3">{{rating_trail3}}</label>
</fieldset>


<fieldset>
  <legend>{{rating_overgrowth}}</legend>

  <input type="radio" id="Overgrowth0" name="Overgrowth" value="0" checked="checked">
  <label for="Overgrowth0">{{rating_overgrowth0}}</label>

  <input type="radio" id="Overgrowth1" name="Overgrowth" value="1">
  <label for="Overgrowth1">{{rating_overgrowth1}}</label>

  <input type="radio" id="Overgrowth2" name="Overgrowth" value="2">
  <label for="Overgrowth2">{{rating_overgrowth2}}</label>

  <input type="radio" id="Overgrowth3" name="Overgrowth" value="3">
  <label for="Overgrowth3">{{rating_overgrowth3}}</label>
</fieldset>


<fieldset>
  <legend>{{rating_elevation}}</legend>

  <input type="radio" id="Elevation0" name="Elevation" value="0" checked="checked">
  <label for="Elevation0">{{rating_elevation0}}</label>

  <input type="radio" id="Elevation1" name="Elevation" value="1">
  <label for="Elevation1">{{rating_elevation1}}</label>

  <input type="radio" id="Elevation2" name="Elevation" value="2">
  <label for="Elevation2">{{rating_elevation2}}</label>

  <input type="radio" id="Elevation3" name="Elevation" value="3">
  <label for="Elevation3">{{rating_elevation3}}</label>
</fieldset>


<fieldset>
  <legend>{{rating_difficulty}}</legend>

  <p>({{rating_difficulty_desc}})</p>

  <input type="radio" id="Difficulty1" name="Difficulty" value="1" checked="checked">
  <label for="Difficulty1">{{rating_difficulty0}}</label>

  <input type="radio" id="Difficulty2" name="Difficulty" value="2">
  <label for="Difficulty2">{{rating_difficulty1}}</label>

  <input type="radio" id="Difficulty3" name="Difficulty" value="3">
  <label for="Difficulty3">{{rating_difficulty2}}</label>

  <input type="radio" id="Difficulty4" name="Difficulty" value="4">
  <label for="Difficulty4">{{rating_difficulty3}}</label>

  <input type="radio" id="Difficulty5" name="Difficulty" value="5">
  <label for="Difficulty5">{{rating_difficulty4}}</label>
</fieldset>

<input type="hidden" name="Rating" value="TRUE">

<div class="rating-buttons">
  <input type="submit" value="{{rating_submit}}" class="btn btn-primary">
  <input type="reset" value="{{rating_reset}}" class="btn btn-default">
</div>


<p>({{rating_disclaimer}})</p>

