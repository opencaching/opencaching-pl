<?php

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	*  UTF8 remaider Ĺ›Ä…Ĺ‚Ăł
	***************************************************************************/


require_once('./lib/common.inc.php');

?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Cache" title="Cache" align="middle"/>&nbsp;Masowe dodawanie logów</div>
<p>
Jeśli masz plik logu z Garmina z zapisem swoich poszukiwań tutaj możesz go załadować na stronę.<br />
Wybierz i prześlij plik, by zobaczyć zestawienie skrzynek i móc szybko zalogować je w serwisie.<br />
Maksymalny rozmiar pliku to 50KB.<br />
</p>
<br />
<form enctype="multipart/form-data" method="POST" action="log_cache_multi.php">
<input type="hidden" name="MAX_FILE_SIZE" value="51200" />
Wskaż plik: <input name="userfile" type="file" size="100"/>
<input type="submit" value="Wyślij"  style="width: 100px" />
</form>