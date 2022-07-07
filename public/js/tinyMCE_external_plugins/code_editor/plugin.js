//https://github.com/Somonator/tinymce5-code-editor-plugin
tinymce.PluginManager.add('code_editor', function(editor, url) {
    let dialog_iframe = null;

    function get_content() {
        return editor.getContent({ source_view: true });
    }

    function set_content(code) {
        editor.focus();
        editor.undoManager.transact(function () {
            editor.setContent(code);
        });
        editor.selection.setCursorLocation();
        editor.nodeChanged();
    }

    function send_data_iframe(data) {
        let ifr = dialog_iframe.contentWindow;

        ifr.postMessage(data, '*');
    }
    
    function open_code_editor() {
        editor.windowManager.openUrl({
            title: 'Source Code',
            url: url + '/dialog/dialog.html',
            buttons: [
                {
                    type: 'cancel',
                    text: 'Cancel'
                },
                {
                    type: 'custom',
                    text: 'Save',
                    name: 'submit',
                    primary: true
                }
            ],
            onAction: function(api, data) {
                if (data.name === 'submit') {
                    let params = {
                        action: 'request_code'
                    };
        
                    send_data_iframe(params); 
                }
            },
            onMessage: function(api, data) {
                if (data.mceAction === 'insert_code') {
                    set_content(data.data.code);
                    api.close();
                }
            }
        });

        dialog_iframe = document.querySelector('.tox-dialog iframe');

        dialog_iframe.addEventListener('load', function() {
            let params = {
                action: 'code_for_edit',
                code: get_content()
            };

            send_data_iframe(params);
        });
    };

    editor.ui.registry.addButton('code_editor', {
        icon: 'sourcecode',
        tooltip: 'Source code',
        onAction: function () {
            return open_code_editor();
        }
    });

    return {
        getMetadata: function () {
            return {
                name: 'Code editor'
            };
        }
    };
});