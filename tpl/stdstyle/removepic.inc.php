<?php

tpl_set_var('mail_oc', $mail_oc);

$submit = tr('remove_pic_subject');

$message_title_internal = tr('remove_pic_title_internal');
$message_internal = tr('remove_pic_internal');

$message_title_toobig = 'Plik za duży';
$message_toobig = 'Wybrany plik jest za duży. Postaraj się zmniejszyć go poprzez zmiane rodzielczości lub jakości.';

$message_title_wrongext = 'Typ pliku nieropoznany';
$message_wrongext = 'Wybrany plik ma nieznany format. Dozwolone są tylko typy BMP, GIF, PNG i JPG - dla zdjęć JPG rekomendowany. Dodatkowow dozwolona wielkość pliku to 300 kB. Rekomendowana rozdzielczosc to 480 pikseli szerokość i 360 piksesli wysokość.';
?>
