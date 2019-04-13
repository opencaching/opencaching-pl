/**
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
function ocUpload(params, callback) {

  var uploadInstance = {
    container: null,          // the main container
    dialogContentTpl: null,   // template of the dialog
    previewEntryTpl: null,    // template of preview enty
    filesToUpload: {},        // internal list of files

    /**
     * pluging initialization
     */
    init: function(){

      if (!params.submitUrl){
        this.returnFail("No url to submit files?!")
      }

      if (!this.isBrowserCompatibile()){
        this.returnFail("Incompatible browser");
      }

      params.formattedMaxFileSize = this.formatBytes(params.maxFileSize);
      params.multiplyFilesAllowed = (params.maxFilesNumber > 1);

      this.displayContainer();
      this.initCloseButton();
      this.initFileBox();
    },

    /**
     * create main container for upload dialog
     */
    displayContainer: function(){
      this.container = $('<div id="upload_container"></div>');
      $('body').append(this.container);

      if(!this.dialogContentTpl){
        var dialogContentHtml = $("#upload_chunkDialogTpl").html();
        this.dialogContentTpl = Handlebars.compile(dialogContentHtml);
      }

      this.container.html(this.dialogContentTpl(params));
    },

    /**
     * close btn initialization
     */
    initCloseButton: function() {
      var _this = this;
      $('#upload_btnClose').click(function(){
        _this.returnFail('Close btn clicked!');
      });
    },

    /**
     * progressBar handler
     * progressLevel should be a number from range <0;100>
     */
    updateProgress: function(progressLevel) {

      console.log('progress:', progressLevel);

      if (progressLevel == 0) { // beginning of upload
        $('#upload_dragFileBox').hide();
        $('#upload_progressBar').show();
      }

      var progressDiv = $('#upload_progressDone')
      var outWidth = $('#upload_progressBar').width();

      progressDiv.width( progressLevel+'%' );

    },

    /**
     * fileBox initialization
     */
    initFileBox: function() {
      _this = this;
      var filedrag = $("#upload_dragFileBox");
      var fileInput = $('#upload_fileInput');

      filedrag.on("dragover", function(e) { _this.fileDragHover(e); });
      filedrag.on("dragleave", function(e) { _this.fileDragHover(e); });
      filedrag.on("drop", function(e) { _this.fileAttached(e); });
      filedrag.click(function(){
        fileInput.trigger('click');
      });

      fileInput.on("change", function(e) { _this.fileAttached(e); });
    },

    /**
     * Handle dragover over uploadBox
     */
    fileDragHover: function(e){
      e.stopPropagation();
      e.preventDefault();
      e.target.className = (e.type == "dragover" ? "dragover" : "");
    },

    /**
     * Called when file is attached
     */
    fileAttached: function(e){

      // cancel event and dragover styling
      this.fileDragHover(e);

      // fetch FileList object
      var files = e.target.files || e.originalEvent.dataTransfer.files;

      // process all File objects
      for (var i = 0, f; f = files[i]; i++) {
        // check files number
        if(Object.keys(this.filesToUpload).length >= params.maxFilesNumber){
          console.error('too many files selected');
          return;
        }

        if(this.filesToUpload[f.name]){
          // file with the same name - skip such file
          console.warn('Same file again?: ', f.name);
          continue;
        }
        this.addFileToPreviewList(f);
      }
    },

    /**
     * Called in loop for every attached file
     */
    addFileToPreviewList: function(file){
      console.log("File:", file);

      var previewEntryData = {
          file: file,
          size: this.formatBytes(file.size),
      };

      //check file type
      if(params.allowedTypesRegex && !file.type.match(params.allowedTypesRegex)){
        // not allowed types
        previewEntryData.error = true;
        previewEntryData.errorType = true;

      } else if(params.maxFileSize < file.size) { // check file size
        // not allowed size
        previewEntryData.error = true;
        previewEntryData.errorSize = true;
      }

      // error occured
      if(previewEntryData.error){
        previewEntryData.src = '/images/icons/attention.svg';
        this.loadPreviewEntry(previewEntryData);
        return;
      }

      // file checked - seems to be OK
      this.addFileToUploadList(file);

      if(file.type.match('image/*')){ //this is image
        var reader = new FileReader();
        var _this = this;
        reader.onload = function(event){
          previewEntryData.src = event.target.result;
          _this.loadPreviewEntry(previewEntryData);
        }
        reader.readAsDataURL(file);
      } else { // non-image file
        previewEntryData.src = '/images/icons/plus.svg';
        this.loadPreviewEntry(previewEntryData);
      }
    },

    /**
     * Create entry on list of upload previews
     */
    loadPreviewEntry: function(entryData) {
      if(!this.previewEntryTpl){
        var previewEntryHtml = $("#upload_filePreviewTpl").html();
        this.previewEntryTpl = Handlebars.compile(previewEntryHtml);
      }

      var html = this.previewEntryTpl(entryData);
      $('#upload_previewBox').append(html);

      if(entryData.error){
        // just remove previewEntry from the list
        $('#upload_previewBox div[data-filename="'+entryData.file.name+'"] .upload_previewEntryRemove')
          .click( function(e){
            $(e.target).parent().remove();
          });

      } else {
        // remove file from the list of files to upload + remove from the list
        var _this = this;
        $('#upload_previewBox div[data-filename="'+entryData.file.name+'"] .upload_previewEntryRemove')
          .click( function(e){
            var filename = $(e.target).parent().data('filename');
            _this.removeFromUploadList(filename);
            $(e.target).parent().remove();
          });
      }
    },

    /**
     * handle process of adding file to plugin's internal files list
     */
    addFileToUploadList: function(file){
      this.filesToUpload[file.name] = file;
      // activate the upload button
      var btn = $('#upload_uploadBtn.btn-disabled');
      if(btn) {
        btn.removeClass('btn-disabled');
        var _this = this;
        btn.click(function(e){
            _this.startUpload();
        });
      }
    },

    /**
     * this func handles removing file from plugin's internal files list
     */
    removeFromUploadList: function(filename){
      delete this.filesToUpload[filename];

      if(Object.keys(this.filesToUpload).length <= 0){
        // there is no files to upload - disable upload button
        var btn = $('#upload_uploadBtn');
        btn.addClass('btn-disabled');
        btn.unbind();
      }
    },

    /**
     * uploading of internal list of files to the server and handling the response
     */
    startUpload: function(){

      var formData = new FormData();
      $.each(this.filesToUpload, function(key, file){
        formData.append(params.formVarName+'[]', file);
      });

      var xhr = new XMLHttpRequest();
      xhr.responseType = "json";

      var _this = this;

      // progress callback
      xhr.upload.onprogress = function(e){
        var progress = Math.round(100 * e.loaded / e.total);
        _this.updateProgress(progress);
      };

      // when sending done
      xhr.addEventListener("load", function(e){

        if (xhr.readyState === xhr.DONE) {
          if (xhr.status === 200) {
            console.log(xhr.response);
            _this.returnSuccess(xhr.response);
            return;
          }else{
            console.error('status:',xhr.status, ' rsp:', xhr.response);
            _this.returnFail(
                (xhr.response)?(xhr.response):'error code:'+xhr.status);
            return;
          }
        }

        console.error('strange - request not done?!')
        _this.returnFail('request not done');
      });

      // when error occured on sending
      xhr.addEventListener("error", function(e){
        console.error('XHR upload error!', e);
        _this.returnFail('Upload error!');
      });

      // call on sending abort
      xhr.addEventListener("abort", function(e){
        console.log('XHR aborted');
        _this.returnFail('Upload aborted');
      });

      xhr.open("POST", params.submitUrl);
      this.updateProgress(0);
      xhr.send(formData);
    },

    /**
     * Check if browser is compatibile with this upload implementation
     */
    isBrowserCompatibile() {
      if(!window.File || !window.FileList || !window.FileReader){
        console.error('Browser not support File|FileList|FileReader!');
        return false;
      }

      var xhr = new XMLHttpRequest();
      if (!xhr.upload) {
        console.error('XHR2 not supported?!');
        return false;
      }

      return true;
    },

    returnFail: function(msg) {
      this.container.remove();
      callback({
        success: false,
        message: msg,
      });
    },

    returnSuccess: function(files) {
      this.container.remove();
      callback({
        success: true,
        newFiles: files,
      });
    },

    /**
     * This function returns bytes number formatted as human-readable value
     */
    formatBytes: function(bytes,base){
      if(bytes == 0) {
        return "0B";
      }
      var c = 1024;
      var d = base||2;
      var e = ["B","kB","MB","GB","TB","PB","EB","ZB","YB"];
      var f = Math.floor(Math.log(bytes)/Math.log(c));
      return parseFloat((bytes/Math.pow(c,f)).toFixed(d))+" "+e[f]
    }

  };

  // run upload dialog
  uploadInstance.init();
}