<?php

  // Unicode Reminder メモ

?>
<script type="text/javascript">
<!--
    // Function sets image URL in FCKeditor
    function SelectFile(fileUrl, thumbUrl)
    {
        if (document.getElementById("insertthumb").checked == true)
            opener.fileBrowserReturn(thumbUrl);
        else
            opener.fileBrowserReturn(fileUrl);

        window.close();
    }

    function CancelSelect()
    {
        window.close();
    }
//-->
</script>
<br />
<table width="100%">
    <tr>
        <td class="header-small"><img src="tpl/stdstyle/images/blue/picture.png" height="32px" width="32px" alt="Obrazki" /> Obrazki dla {cachename}</td>
    </tr>
</table>
<table>
    {{pictures}}
</table>
<p><input type="checkbox" id="insertthumb" style="border:0;" /> <label for="insertthumb">Podgląd obrazka</label></p>
<a href="javascript:CancelSelect();"><img border="0" height="16px" width="16px" src="tpl/stdstyle/images/log/16x16-stop.png" alt="" /></a> <a href="javascript:CancelSelect();">Przerwij</a>
