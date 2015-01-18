<?php
$secret = "dupa231";
include('commons.php');
header('Content-Type: application/xhtml+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
echo '<?xml-stylesheet type="text/css" href="style.css"?'.">\n";


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl">
    <script src="ajax.js" type="text/javascript" charset="utf-8"></script>
    <head>
        <title>Generator LogBook
        </title>
    </head>
    <body>
        <script type="text/javascript">
            //<![CDATA[

            var cururl;
            var curpage = 1;
            var numpages = 1;

            function startCallback() {
                // make something useful before submit (onStart)
                return true;
            }

            function setPreview(page) {
                document.getElementById('previewimg').src = cururl + "-page" + page + ".jpg";
                document.getElementById('previewblock').getElementsByTagName('span')[curpage - 1].className = '';
                document.getElementById('previewblock').getElementsByTagName('span')[page - 1].className = 'selected';
                curpage = page;
            }

            function bindArgument(fn, arg)
            {
                return function () {
                    return fn(arg);
                };
            }

            function removeChildrenFromNode(node)
            {
                var len = node.childNodes.length;
                while (node.hasChildNodes()) {
                    node.removeChild(node.firstChild);
                }
            }

            function updatePreview(newurl) {
                cururl = newurl;
                pages = document.getElementById('previewpages');
                removeChildrenFromNode(pages);

                for (var i = 1; i <= numpages; i++) {
                    var s = document.createElement('span');
                    s.innerHTML = i;
                    s.onclick = bindArgument(setPreview, i);
                    pages.appendChild(s);
                }
                setPreview(1);
            }

            function completeCallback(response) {
                alert(response);
                document.getElementById('previewpageselect').style.visibility = 'visible';
                var splitted = response.split(",");
                if (splitted[2] == 'pdf') {
                    window.location = splitted[1] + ".pdf";
                }
                else {
                    numpages = splitted[0];
                    updatePreview(splitted[1]);
                }
            }

            function onSubmit(obj) {
                if (obj.d1.value && obj.d2.value && obj.m1.value && obj.m2.value)
                    obj.coords.value = "N " +
                            obj.d1.value +
                            "° " +
                            obj.m1.value +
                            "' E " +
                            obj.d2.value +
                            "° " + obj.m2.value + "'";

                return AIM.submit(obj, {'onStart': startCallback, 'onComplete': completeCallback});
            }

            //]]>
        </script>
        <div id="logoblock">
            <img src="geocaching.jpg" id="logo" />
        </div>
        <div id="navibar">
    <!--<span><a href="">Strona Główna</a></span>-->
            <?php
            include("menu.inc");
            ?>
        </div>
        <p>

            <?php

            ?>

            <div id="previewblock">
                <div id="previewpageselect">
                    Podgląd:
                    <div id="previewpages"></div>
                </div>
                <div id="preview<?echo (int)$_GET['logbook_type'];?>
                     ">
                    <img src="" id="previewimg" />
                </div>
            </div>
            <div>

                <form method="get" action="index.php">
                    <label for="logbook_type">Rodzaj: </label>
                    <select name="logbook_type" id="logbook_type" onchange="parentNode.submit();">
                        <?php
                        $_GET['logbook_type'] = (int)$_GET['logbook_type'];
                        if(!($_GET['logbook_type'] > 0 && (int)$_GET['logbook_type']<= 2)) print '<option value="0" selected="true">Wybierz</option>';?>
                        <option value="1" <?if($_GET['logbook_type'] == 1) print "selected=\"true\"";?>>Logbook A6 Dwustronny</option>
                        <option value="2" <?if($_GET['logbook_type'] == 2) print "selected=\"true\"";?>>Logbook A7 Dwustronny</option>
                    </select>
                </form>
                <hr />
                <?php
                if($_GET['logbook_type'] == 0) {
                print 'Proszę wybrać typ logbooku.';
                }
                else if($_GET['logbook_type'] == 1) {
                print
                '<span class="note">Zostaw puste pola jeśli chcesz je wspisać później ręcznie lub też zostawić wartości standardowe.</span>
                <form id="logbookopts" action="remotelogbook.php" method="post" onsubmit="return onSubmit(this);" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                <input type="hidden" name="secret" value="' .encrypt($_GET['logbook_type'] . " This is a secret message", $secret) . '" />
                <p><label for="cache_name">Nazwa skrzynki:</label> <input type="text" maxlength="80" name="cache_name" id="cache_name" /></p>
                <p><label for="coords">Współrzędne:</label> <input type="hidden" maxlength="80" name="coords" id="coords" /> N <input type="text" name="d1" maxlength="3" class="degrees" /> ° <input class="minutes" maxlength="6" name="m1" type="text" /> \' E <input class="degrees" maxlength="3" name="d2" type="text" /> ° <input class="minutes" maxlength="6" name="m2" type="text" /> \'</p>
                <p><label for="image_file">Obrazek tytułowy:</label> <input type="file" name="image_file" id="image_file" class="file" /><br />
                <span class="posmod"> x <input type="text" name="x1" value="0.0" /> y <input type="text" name="y1" value="0.0" /> w <input type="text" name="w1" value="0.0"/> h <input type="text" name="h1" value="0.0"/></span>
                </p>
                <p><label for="image_file">Obrazek w tle:</label> <input type="file" name="bgimage_file" id="bgimage_file" class="file" /><br />
                <div>
                <span class="posmod"> x <input type="text" name="x2" value="0.0" /> y <input type="text" name="y2" value="0.0" /> w <input type="text" name="w2" value="0.0"/> h <input type="text" name="h2" value="0.0"/> Widoczność <input type="text" name="opacity" value="20" />% </span>
                <br />
                <span class="note">Modyfikatory dla obrazka: np. -2 w polu <strong>x</strong> spowoduje przesunięcie o 2 piksele w lewo, a 4 w <strong>h</strong> zwiększy wysokośc o 4 piksele.</span>
                </div>
                </p>
                <p><label for="nick">Bez ozdób FTF:</label> <input type="checkbox" name="noftf" id="noftf" /></p>
                <!--        <p><label for="nick">Bez ramek:</label> <input type="checkbox" name="noborders" id="noborders" />
                <span class="note">przydatny gdy jest trudnośc w równym wydrukowaniu obu stron, ucina jedynie na stronach nieparzystych</span></p>-->
                <p><label for="nick">Nick:</label> <input type="text" name="nick" id="nick" /></p>
                <p><label for="nick">E-Mail:</label> <input type="text" name="email" id="email" />
                <span class="note">wykorzystywany jest tylko do uzupełnienia pola w Log Booku</span></p>
                <p><input type="submit" onclick=\'document.getElementById("pdf").value = ""\'  name="gen_log" value="Podgląd" /><input type="submit" name="gen_log" onclick=\'document.getElementById("pdf").value = "pdf"\' value="Generuj PDF" /></p>
                <input type="hidden" name="pdf" id="pdf" />
                </form>
                ';
                }
                else if($_GET['logbook_type'] == 2) {
                print
                '<span class="note">Zostaw puste pola jeśli chcesz je wspisać później ręcznie lub też zostawić wartości standardowe.</span>
                <form id="logbookopts" action="remotelogbook.php" method="post" onsubmit="return onSubmit(this);" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                <input type="hidden" name="secret" value="' .encrypt($_GET['logbook_type'] . " This is a secret message", $secret) . '" />
                <p><label for="cache_name">Nazwa skrzynki:</label> <input type="text" maxlength="80" name="cache_name" id="cache_name" /></p>
                <p><label for="coords">Współrzędne:</label> <input type="hidden" maxlength="80" name="coords" id="coords" /> N <input type="text" name="d1" maxlength="3" class="degrees" /> ° <input class="minutes" maxlength="6" name="m1" type="text" /> \' E <input class="degrees" maxlength="3" name="d2" type="text" /> ° <input class="minutes" maxlength="6" name="m2" type="text" /> \'</p>
                <p><label for="image_file">Obrazek tytułowy:</label> <input type="file" name="image_file" id="image_file" class="file" /><br />
                <span class="posmod"> x <input type="text" name="x1" value="0.0" /> y <input type="text" name="y1" value="0.0" /> w <input type="text" name="w1" value="0.0"/> h <input type="text" name="h1" value="0.0"/></span>
                </p>
                <p><label for="image_file">Obrazek w tle:</label> <input type="file" name="bgimage_file" id="bgimage_file" class="file" /><br />
                <div>
                <span class="posmod"> x <input type="text" name="x2" value="0.0" /> y <input type="text" name="y2" value="0.0" /> w <input type="text" name="w2" value="0.0"/> h <input type="text" name="h2" value="0.0"/> Widoczność <input type="text" name="opacity" value="20" />% </span>
                <br />
                <span class="note">Modyfikatory dla obrazka: np. -2 w polu <strong>x</strong> spowoduje przesunięcie o 2 piksele w lewo, a 4 w <strong>h</strong> zwiększy wysokośc o 4 piksele.</span>
                </div>
                </p>
                <p><label for="nick">Bez ozdób FTF:</label> <input type="checkbox" name="noftf" id="noftf" /></p>
                <p><label for="nick">Bez ramek:</label> <input type="checkbox" name="noborders" id="noborders" />
                <span class="note">przydatny gdy jest trudnośc w równym wydrukowaniu obu stron, ucina jedynie na stronach nieparzystych</span></p>
                <p><label for="nick">Nick:</label> <input type="text" name="nick" id="nick" /></p>
                <p><label for="nick">E-Mail:</label> <input type="text" name="email" id="email" />
                <span class="note">wykorzystywany jest tylko do uzupełnienia pola w Log Booku</span></p>
                <p><input type="submit" onclick=\'document.getElementById("pdf").value = ""\'  name="gen_log" value="Podgląd" /><input type="submit" name="gen_log" onclick=\'document.getElementById("pdf").value = "pdf"\' value="Generuj PDF" /></p>
                <input type="hidden" name="pdf" id="pdf" />
                </form>
                ';
                }
                else {
                print 'Wrong logbook type!';
                }
                ?>
            </div>
        </p>
    </body>
</html>
