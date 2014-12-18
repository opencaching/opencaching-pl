<?
$secret = "dupa231";
include('commons.php');
header('Content-Type: application/xhtml+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
echo '<?xml-stylesheet type="text/css" href="style.css"?'.">\n";

if (get_magic_quotes_gpc()) {
function stripslashes_deep($value)
{
$value = is_array($value) ?
array_map('stripslashes_deep', $value) :
stripslashes($value);

return $value;
}

$_POST = array_map('stripslashes_deep', $_POST);
$_GET = array_map('stripslashes_deep', $_GET);
$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl">
    <head>
        <title>Naprawiacz opisu</title>
        <script src="ajax.js" type="text/javascript" charset="utf-8"></script>
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

            //]]>
        </script>
        <div id="logoblock">
            <img src="geocaching.jpg" id="logo" />
        </div>
        <div id="navibar">
    <!--<span><a href="">Strona Główna</a></span>-->
            <?
            include("menu.inc");
            ?>
        </div>
        <p>

            <?

            $options = array("input-encoding" => "utf8", "output-encoding" => "utf8", "output-xhtml" => true, "doctype" => "omit", "show-body-only" => true, "char-encoding" => "utf8", "quote-ampersand" => true, "quote-nbsp" => true);
            $text = $_POST['text'];
            //$text = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $text);
            #$text = preg_replace('~&#x([0-9a-fA-F]+);~ei', 'chr(hexdec("\\1"))', $text);
            //$text = mb_convert_encoding( $text, "utf-8", "HTML-ENTITIES" );

            //decode decimal HTML entities added by web browser
            $text = preg_replace('/&#\d{2,5};/ue', "utf8_entity_decode('$0')", $text );
            //decode hex HTML entities added by web browser
            $text = preg_replace('/&#x([a-fA-F0-7]{2,8});/ue', "utf8_entity_decode('&#'.hexdec('$1').';')", $text );

            //callback function for the regex
            function utf8_entity_decode($entity){
            $convmap = array(0x0, 0x10000, 0, 0xfffff);
            return "ż";
            // return mb_decode_numericentity($entity, $convmap, 'UTF-8');
            }


            $tidy =  tidy_parse_string(html_entity_decode($text, ENT_NOQUOTES, "UTF-8"), $options);
            tidy_clean_repair($tidy);



            function iterate_over($node)
            {
            $removed = array();

            print "<br />Iterating over " .  $node->tagName ."\n";

            if($node->tagName == "span") {
            print "deleting\n";
            array_push($removed, $node);
            }
            if(!$node)
            return;

            if($node->hasAttributes()) {
            $attributes = $node->attributes;
            if(!is_null($attributes))
            foreach ($attributes as $index=>$attr)
            echo $attr->name ." = " . htmlspecialchars($attr->value) . "\n";
            }
            if($node->hasChildNodes()) {
            $children = $node->childNodes;
            foreach($children as $child) {
            $removed = array_merge($removed, iterate_over($child, $array));
            }
            }
            return $removed;
            }

            function appendSibling(DOMNode $newnode, DOMNode $ref)
            {
            if ($ref->nextSibling) {
            // $ref has an immediate brother : insert newnode before this one
            return $ref->parentNode->insertBefore($newnode, $ref->nextSibling);
            } else {
            // $ref has no brother next to him : insert newnode as last child of his parent
            return $ref->parentNode->appendChild($newnode);
            }
            }

            function remove_node($domElement)
            {
            if($domElement->hasChildNodes()) {
            $children = $domElement->childNodes;
            $toAppend = array();
            foreach($children as $child)
            array_unshift($toAppend, $child);
            foreach($toAppend as $child)
            appendSibling($child, $domElement);
            }
            //  $domElement->parentNode->removeChild($domElement);
            }


            $str = (string)$tidy;
            if($str) {
            //  $str = str_replace("&amp;", "&", $str);
            $doc = DOMDocument::loadXML("<cache_description>".$str."</cache_description>");
            $doc->encoding = "utf-8";
            $main = $doc->documentElement;

            if($main) {
            $for_removal = iterate_over($main);
            foreach($for_removal as $domElement) {
            echo "<br/>removing ..\n";
            remove_node($domElement);
            $domElement->parentNode->removeChild($domElement);
            }
            }

            $str = $doc->saveXML();
            $str = str_replace('<?xml version="1.0" encoding="utf-8"?>'."\n", "", $str);
            $str = str_replace('<cache_description>', "", $str);
                $str = str_replace('</cache_description>', "", $str);
            }

            ?>
            <form method="post" action="index.php?page=cachevalidator">
                <textarea name="text" id="validatorarea"><?

echo htmlspecialchars($str, ENT_NOQUOTES, "UTF-8");

?></textarea>
                <br/>
                <input type="submit" name="submit" value="Poprawiaj!" />
            </form>
        </p>
        <div id="textpreview">
            <?
            echo $str;




            ?>
        </div>
    </body>
</html>
