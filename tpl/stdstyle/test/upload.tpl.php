
<div>
  <div id="uploadA" class="btn">Upload!</div>
</div>

<hr/>
<div>Results:
  <div id="results">Click button to upload something</div>
</div>

<script>
  $('#uploadA').click(function(e){

    /*
      ocUpload takes two params:
       - params json - see UploadModel
       - callback function

      on end of upload callback will be called with JSON param:
        {
          success: true|false,                // true on success | false on error
          message: 'error-description',       // tech. error description in english (usually not for end-user) (only on fail)
          newfiles: ['fileA','fileB','fileC'] // list of urls to new files saved on server (only on success)
        }
    */
    ocUpload(<?=$view->uploadModelJson?>, function(uploadResult){

      if(uploadResult.success){
        //upload successed
        $('#results').html('');
        $('#results').append('<div>SUCCESS - new files:</div><ul>');
        $.each(uploadResult.newFiles, function(key,val){
          $('#results').append('<li><a href="'+val+'">'+val+'</a></li>');
        });
      } else {
        // upload fail
        $('#results').html('FAIL: '+uploadResult.message);
      }

    });
  });

</script>
