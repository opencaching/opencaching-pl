let textarea = document.getElementById('code');

setTimeout(function() {
    let codemirror = document.querySelector('.CodeMirror');

    if (codemirror === null) {
        textarea.style.display = 'block';
        textarea.value = 'Editor is not loaded';
    }
}, 1000);

function init_editor() {
    let opts = {
        theme: 'monokai',
        mode: 'htmlmixed',
        inputStyle: 'contenteditable',
        indentWithTabs: true,        
        indentUnit: 2,
        tabSize: 2,        
        lineNumbers: true,
        lineWrapping: true,
        matchBrackets: true,
        styleActiveLine: true,       
        saveCursorPosition: true     
    };

    let codemirror = CodeMirror.fromTextArea(textarea, opts);

    codemirror.isDirty = false;
    codemirror.on('change', function(editor) {
        editor.isDirty = true;
        textarea.value = editor.getValue();
    });
}

function send_code() {
    window.parent.postMessage({
        mceAction: 'insert_code',
        data: {
            code: textarea.value
        }
    }, '*');
}

window.addEventListener('message', function (event) {
    let data = event.data;

    if (data.action === 'code_for_edit') {
        textarea.style.display = 'block';
        if (data.code) textarea.value = data.code;

        init_editor();
    } else if (data.action === 'request_code') {
        send_code();
    }
});