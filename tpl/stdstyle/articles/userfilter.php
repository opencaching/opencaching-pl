    <form name="FindUser" style="display:inline;" action="" onsubmit="return false;">
        <table  class = "GCT-div-table" >
            <tr >
                <td>
                        <?php echo tr('user')?>:&nbsp&nbsp
                </td>
                <td>
                    <input type="text" name="User" value="" style="width:100px; text-align: left; ">
                    &nbsp&nbsp&nbsp<button type="submit" value=<?php echo tr('search') ?> name="bFindUser" style="font-size:12px;width:70px;"; onClick ="GCTStatsFindUser( document.FindUser.User.value )"  /><?php echo tr('search') ?></button>
                </td>
            </tr>
            <tr >
                <td align="right">
                    <?php echo tr('Position')?>:&nbsp&nbsp
                </td>
                <td>
                    <input type="text" name="FUPosition" id="FUPosition" class="GCT-div-readOnly" style="width:70px" readonly >
                </td>

            </tr>
        </table>
        </form>



