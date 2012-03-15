Przez usunięcie mam w tej chwili na myśli "nie commitowanie". Czyli przy
checkoutowaniu nowej kopii te pliki nie będą istnieć. To są bardziej notatki
dla mnie samego, ale commituję i tak na chwilę.

Usunięte wszystko wewnątrz logbook/work.
=> Sam katalog work zostawiam (chmod 777) i ustawiam svn:ignore na wszystko w środku.
=> Nie jest to najlepsze rozwiązanie, bo ten katalog powinien znajdować się
=> w ścieżce na pliki dynamiczne, ale zostawiam to autorowi do poprawy.
OK

Usunięte symboliczne linki:
- images/uploads
- upload
- uploads
Wszystkie kierowały do /var/www/ocpl-data/images/uploads/
=> Zastąpiłem wpisami "Alias" w konfiguracji serwera
OK

Usunięty symlink:
- wigo
Kierował do /var/www/ocpl-data/wigo
=> Zastąpiłem wpisem "Alias" w konfiguracji serwera
OK

Usunięty lib/cgi-bin/data/mapper.ini (dodany do svn:ignore).
=> Skopiowany z poprzedniego WS. Przydałoby się zrobić tak, żeby korzystał z lib/settings.inc.php,
=> zamiast z oddzielnego pliku.
OK

Usunięty lib/settings.inc.php (dodane do svn:ignore).
=> Skopiowany z poprzedniego WS.
OK

Commitowane OKAPI. Teraz instalowanie nowej wersji OKAPI będzie polegalo na commitowaniu kopii
do OCPL. Później może zdecyduję się zrobić przez svn:externals.
OK

Pełna lista plików, które były na produkcji, ale zostały usunięte (nie zostały dodane do SVNa,
więc teraz ich już na produkcji nie ma). Tam gdzie jest "Entering directory" usunąłem cały katalog:

    25: 818c017561a9b7788484e2f92b8f1029  ocpl - Export\doc\sql\static-data\data.sql
   118: 795b6858a509dca034fcd52323fc01a7  ocpl - Export\doc\bgc.pdf
   127: 732b58df1f953b935be7af2dc3c79b85  ocpl - Export\doc\ocpl-icons.zip
   147: dc193388d67c96388fb26024cde12eb9  ocpl - Export\gpx\1\ocgpx.xsd~
   971: 1a7d127e299ec1c2b1c7f1ab9e2c14b1  ocpl - Export\images\uploads
  1061: c553a360214638956a561ce5538e49cc  ocpl - Export\lib\cgi-bin\data\liberation.zip
  1066: 5c1dbe991aa5ef9c72ac94ed120fe93a  ocpl - Export\lib\cgi-bin\data\mapper.ini
  1335: 9b21f39ad5701ff43cb2474243577777  ocpl - Export\lib\cgi-bin\config.o
  1338: 0b059718ca1fb8e8877e8356f084a907  ocpl - Export\lib\cgi-bin\hashtable.o
  1341: 6d31aab8e43132ef699e029e7d3b57e4  ocpl - Export\lib\cgi-bin\hashtable_itr.o
  1345: 34c393e1a95a4504931d48e126b44759  ocpl - Export\lib\cgi-bin\IMG_savepng.o
  1351: 641a5309f65ecc6cc5bb76663e0623fd  ocpl - Export\lib\cgi-bin\mapper.fcgi.old
  1352: 6d8756f35ee590e582f2131d9bf99d36  ocpl - Export\lib\cgi-bin\mapper.o
  1355: 0953737cf7882c1e58e0a66ec196ba98  ocpl - Export\lib\cgi-bin\microcgi.o
  1899: ;+ Entering directory: ocpl - Export\lib\phpqrcode\temp
  2877: 6fc9535ed3d80cfc785e97fb105c3c72  ocpl - Export\lib\settings.inc.php
  2934: ;+ Entering directory: ocpl - Export\okapi
  3077: ;+ Entering directory: ocpl - Export\old
 11303: f1e0f87a57e3e52b037cc8d4b4aa746f  ocpl - Export\tmp\test.png
 13353: 08bdf03123d31a3cf948c1164b7ced57  ocpl - Export\tpl\stdstyle\svn-commit.tmp
 13460: ;+ Entering directory: ocpl - Export\util.sec\import_nutsold
 13533: 03e2f98bfd9d11ba6ed7206991f98dae  ocpl - Export\xml\.htaccess_old
 13671: 30ba12d780a2b2328050521e195dbfb4  ocpl - Export\svn-commit.2.tmp
 13672: f80284a6355ad84528fc611e715123db  ocpl - Export\svn-commit.tmp
 13677: 1a7d127e299ec1c2b1c7f1ab9e2c14b1  ocpl - Export\upload
 13678: 1a7d127e299ec1c2b1c7f1ab9e2c14b1  ocpl - Export\uploads
 13690: a8e9749276ab1ee4bde780ec3a0b1df6  ocpl - Export\wigo

Lista plików, których NIE było na produkcji, ale były w SVNie i zdecydowałem się
je w SVNie zostawić (więc teraz pojawiły się również na produkcji):

   693: b477fb8630ee92ef287a27a239054240  ocpl - Export\images\head\pano970-0018.jpg
   694: cfef5ac452bb02b679d2c6c16c631f18  ocpl - Export\images\head\pano970-0019.jpg
   695: 4442988b4488bcb8b82f22f98e82284d  ocpl - Export\images\head\pano970-0020.jpg
   696: e74ed22646d7ce6b5bb296f174718c9e  ocpl - Export\images\head\pano970-0021.jpg
   698: 513b889df82dc53164a66620086f616e  ocpl - Export\images\head\pano970-0023.jpg
   704: d069e8ee723a18ac8a4369bab2d26f14  ocpl - Export\images\head\pano970-0029.jpg
   855: 36e82ec008d3778de2abde615e01d03c  ocpl - Export\images\garmin_no.jpg
   866: 953138ad5228b956ceed811b7a08e9ab  ocpl - Export\images\gps-no.png
   868: cf412038241eff31c2308652569978ae  ocpl - Export\images\gps.png
   889: d8720cf3644e6ae0511c1391fb2ecf07  ocpl - Export\images\login-bg.gif

// TESTUJĘ post-commit i automatyczną aktualizację....
