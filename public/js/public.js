$(document).ready(function() {
    $('.js-oc-copy-to-clipboard').click(function() {
        var dataToCopy = $(this).data('copy-to-clipboard');
        copyToClipboard(dataToCopy);
    });

    function copyToClipboard(text) {
        var $tempInput = $('<input>');
        $('body').append($tempInput);
        $tempInput.val(text).select();
        document.execCommand('copy');
        $tempInput.remove();
    }
});
