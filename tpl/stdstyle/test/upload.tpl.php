
<div>
  <div id="uploadA" class="btn">Upload!</div>
</div>

<hr/>
<div>Results: <div id="results"></div></div>

<script>
  $('#uploadA').click(function(e){

    ocUpload(<?=$view->logImgModelJson?>, function(uploadResult){

      console.log('upload returned', uploadResult);

      $('#results').html(uploadResult.filesArr[0]);
    });
  });

</script>
