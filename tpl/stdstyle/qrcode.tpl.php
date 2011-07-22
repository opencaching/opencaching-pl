<?php 

/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************
	  
   Unicode Reminder メモ
	
 ****************************************************************************/
 ?>
 
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/decrypt.png" class="icon32" alt="" title="" align="middle"/>&nbsp;Etykietka z QR Code</div>
<p>&nbsp;</p>

   <div class="searchdiv"><p>&nbsp;</p>
   <center>{imgqrcode}</center>
	<br/><br/>
    <form action="qrcode.php" method="post">
        <span style="font-weight:bold;font-size:14px;width:120px">Dane w QR Code:</span>&nbsp;
	<input name="data" value="{qrcode}" maxlength="77" size="70" style="font-size:14px;"><br/><br/>
	<span style="margin-left:125px;">&nbsp;</span><button type="submit" name="Generuj" value="Generuj" style="font-size:14px;width:120px"><b>Generuj</b></button>
	</form><br/>
<img src="tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info" /><a class="links" href="http://www.i-nigma.com/Downloadi-nigmaReader.html">Tu znajdziesz czytnik QR Code dla różnych telefonów</a>
</div>
