$(document).ready(function() {
    $('.js-oc-copy-to-clipboard').click(function(e) {
        e.preventDefault();
        let dataToCopy = $(this).data('copy-to-clipboard');
        copyToClipboard(dataToCopy);
    });

    function copyToClipboard(text) {
        let $tempInput = $('<input>');
        $('body').append($tempInput);
        $tempInput.val(text).select();
        document.execCommand('copy');
        $tempInput.remove();
    }
});
