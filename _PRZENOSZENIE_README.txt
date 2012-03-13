Usunąłem mnóstwo rzeczy. W szczególności te, które zawierały "tmp" lub "old".
Tutaj wpisuję jedynie te zmiany, które są istotne (wymagają dodatkowych akcji).

Przez usunięcie mam w tej chwili na myśli "nie commitowanie". Czyli przy
checkoutowaniu nowej kopii te pliki nie będą istnieć. To są bardziej notatki
dla mnie samego, ale commituję i tak na chwilę.

Usunięte symboliczne linki:
- images/uploads
- upload
- uploads
Wszystkie kierowały do /var/www/ocpl-data/images/uploads/
=> zastąpić mod_rewrite!

Usunięty symlink:
- wigo
Kierował do /var/www/ocpl-data/wigo
=> zastąpić mod_rewrite!

Usunięty lib/cgi-bin/data/mapper.ini (dodany do svn:ignore).
=> zrobić tak, żeby korzystał z lib/settings.inc.php (ewentualnie skopiować przed switchowaniem)

Usunięte parę niecommitowanych rzeczy z katalogu doc. Wydawały się być niepotrzebne.
=> nie wiem, niech ktoś to przejrzy jak chce; czy ten doc/ w ogóle powinien być w repo?

Usunięty lib/settings.inc.php (dodane do svn:ignore).
=> skopiować przed switchowaniem workspace


