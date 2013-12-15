<?
$secret = 'dupa231';
include('commons.php');

$logbook_type = validate_msg(decrypt($_POST['secret'], $secret));
if(!$logbook_type)
	exit;

// Where the file is going to be placed 
$target_path = "work/";

function file_begin($filename)
{
	return begin(explode(".", $filename));
}

function replace_text_in_file($file, $search, $replace)
{
	$f = fopen($file, 'r');
	if(!$f)
		return;
	while(!feof($f))
		$text .= fread($f, 4096);
	fclose($f);
	$f = fopen($file, 'w');
	if(!$f)
		return;
	$text = str_replace($search, $replace, $text);

	fwrite($f, $text, strlen($text));
	fclose($f);

	return;
}

/* Add the original filename to our target path.  
   Result is "uploads/filename.extension" */
$target_path = $target_path . basename( $_FILES['image_file']['name']); 
$ext = strtolower(substr(strrchr(basename( $_FILES['image_file']['name']), '.'), 1));
$ext2 = strtolower(substr(strrchr(basename( $_FILES['bgimage_file']['name']), '.'), 1));


$filename = $_FILES['image_file']['tmp_name'] . "." . $ext;
$shortname = crc32(uniqid());

if(!move_uploaded_file($_FILES['image_file']['tmp_name'], "/tmp/$shortname.$ext")) {
	$ext = "jpg";
	exec("cp logo.jpg /tmp/$shortname.jpg");
}
if(!move_uploaded_file($_FILES['bgimage_file']['tmp_name'], "/tmp/".$shortname."2.$ext2")) {
	$ext2 = "jpg";
	exec("cp logo2.jpg /tmp/bg".$shortname.".jpg");
}

if($ext != "png" && $ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
	echo "wrong format...";
	exit;
}
if($ext2 != "png" && $ext2 != "jpg" && $ext2 != "jpeg" && $ext2 != "gif" && $ext2 != "bmp") {
	echo "wrong format...";
	exit;
}


$pages = "";
if($logbook_type == 1)
	$imax = 6;
else if($logbook_type == 2)
	$imax = 4;

$extra=0;

for($i=1;$i<=$imax+$extra;++$i) {
	$page=$i;
	if($logbook_type == 1) {
		if($_POST['noftf'] && $i == 4)
			$noftf="_noftf";
		else
			$noftf="";
		exec("cp Logbook-A6-_dwustronny_by_rushcore_page$page$noftf.svg work/$shortname.svg");
	}
	else if($logbook_type == 2) {
		if($_POST['noftf'] && $i == 4)
			$noftf="_noftf";
		else
			$noftf="";
		exec("cp Logbook-A7-_dwustronny_by_rushcore_page$page$noftf.svg work/$shortname.svg");
	}
	replace_text_in_file("work/$shortname.svg", "logo.png", "/tmp/".$shortname."2.$ext");
	replace_text_in_file("work/$shortname.svg", "logo2.png", "/tmp/".$shortname."3.$ext2");
	$cachename = substr($_POST['cache_name'], 0, 80);

	$coords = substr($_POST['coords'], 0, 80);

	$nick = substr($_POST['nick'], 0, 80);
	$email = substr($_POST['email'], 0, 80);

	$svg = new DomDocument;
	$svg->validateOnParse=true;
	$svg->Load("work/$shortname.svg");

	if($_POST['noborders'] && !($i % 2)) {
		for($f = 1;$f<=4;++$f) {
			$elem = $svg->getElementById('frame'.$f);
			if($elem)
				$elem->setAttribute("style", "fill:none;stroke-opacity: 0");	
		}
	}

	$elem = $svg->getElementById('titlelogo');
	$mod = 0;
	if($elem) {
		$elem->setAttribute("height", $elem->getAttribute("height")+(float)$_POST['h1']);
		$elem->setAttribute("width", $elem->getAttribute("width")+(float)$_POST['w1']);
		$elem->setAttribute("x", $elem->getAttribute("x")+(float)$_POST['x1']);
		$elem->setAttribute("y", $elem->getAttribute("y")+(float)$_POST['y1']);
		$mod = 1;
	}
	for($j = 1;$j <= 4;++$j) {
		if($j > 1)
			$bgid = $j;
		else
		$bgid="";
		$elem = $svg->getElementById("bglogo$bgid");
		if($elem) {
			$elem->setAttribute("height", $elem->getAttribute("height")+(float)$_POST['h2']);
			$elem->setAttribute("width", $elem->getAttribute("width")+(float)$_POST['w2']);
			$elem->setAttribute("x", $elem->getAttribute("x")+(float)$_POST['x2']);
			$elem->setAttribute("y", $elem->getAttribute("y")+(float)$_POST['y2']);
			$mod = 1;
		}
	}
	$elem = $svg->getElementById('cachename');
	if($elem) {
		if(strlen($cachename) == 0) {
			$cachename = "...................................................";
			 if($logbook_type == 2 && $_POST['pdf'])
				$cachename = "         " . $cachename; // fix some weird alignment problem
			$elem->setAttribute("style", $elem->getAttribute("style").";-inkscape-font-specification:Nimbus Sans L;font-weight:normal;");
		}
		else
			$elem->setAttribute("style", $elem->getAttribute("style").";-inkscape-font-specification:Nimbus Sans L Bold;font-weight:bold;");
		$obj = $elem->childNodes->item(0);
		$cachename = str_replace("\'", "'", $cachename);
		$obj->replaceData(0, strlen($obj->substringData(0, 9999)), $cachename);
		$mod = 1;
	}
	$elem = $svg->getElementById('coords');
	if($elem) {
		if(strlen($coords) == 0) {
			$coords = "...................................................";
			 if($logbook_type == 2 && $_POST['pdf'])
				$coords = "         " . $coords; // fix some weird alignment problem
			$elem->setAttribute("style", $elem->getAttribute("style").";-inkscape-font-specification:Nimbus Sans L;font-weight:normal;");
		}
		else
			$elem->setAttribute("style", $elem->getAttribute("style").";-inkscape-font-specification:Nimbus Sans L Bold;font-weight:bold;");
		$obj = $elem->childNodes->item(0);
		$coords = str_replace("\'", "'", $coords);
		$obj->replaceData(0, strlen($obj->substringData(0, 9999)), $coords);
		$mod = 1;
	}
	$elem = $svg->getElementById('nick');
	if($elem) {
		$obj = $elem->childNodes->item(0);
		$obj->replaceData(0, strlen($obj->substringData(0, 9999)), $nick);
		$mod = 1;
	}
	$elem = $svg->getElementById('email');
	if($elem) {
		$obj = $elem->childNodes->item(0);
		$obj->replaceData(0, strlen($obj->substringData(0, 9999)), $email);
		$mod = 1;
	}
	if($mod)
		$svg->save("work/$shortname.svg");

	$pid1 = run_in_bg("convert -background white -flatten /tmp/bg$shortname.$ext2 /tmp/".$shortname."2.$ext2");
	$opacity = max(0, min((int)$_POST['opacity'], 100));

	$pid2 = run_in_bg("convert -alpha on -channel o -evaluate set $opacity% -background white -flatten /tmp/".$shortname."2.$ext2 /tmp/".$shortname."3.$ext2 && ".
	"convert -background white -flatten /tmp/$shortname.$ext /tmp/".$shortname."2.$ext");
	wait_for_pid($pid1);
	wait_for_pid($pid2);
#	print system("convert -v -background white -flatten /tmp/bg$shortname.$ext2 /tmp/".$shortname."2.$ext2");
#	print system("convert -alpha on -channel o -evaluate set $opacity% -background white -flatten /tmp/".$shortname."2.$ext2 /tmp/".$shortname."3.$ext2 && ".
#	"convert -background white -flatten /tmp/$shortname.$ext /tmp/".$shortname."2.$ext");


	if(!$_POST['pdf']) {
		if($logbook_type == 1)
			$size = 254;
		else
			$size = 350;

		if($logbook_type == 1) {
			if($i % 2)
				$rot = 270;
			else
				$rot = 90;
			$pid[$i]=run_in_bg("inkscape \"work/$shortname.svg\" -w $size -a 102.702:154.450:640.956:895.851 -e \"work/" . $shortname ."2.png\" &&" . 
				"convert -rotate $rot -background white -flatten \"work/".$shortname."2.png\" \"work/". $shortname."-page$i.jpg\"");
		}
		else {
			$pid[$i]=run_in_bg("inkscape \"work/$shortname.svg\" -w $size -a 102.702:154.450:640.956:895.851 -e \"work/". $shortname."-page$i.jpg\"");
		}

	}
	else {
		$pid[$i]=run_in_bg("inkscape \"work/$shortname.svg\" -A \"work/$shortname-page$i.pdf\"");
		$pages .= "work/$shortname-page$i.pdf ";
	}
}
for($i=1;$i<=$imax+$extra;++$i) {
	wait_for_pid($pid[$i]);
}
if($_POST['pdf'])
	shell_exec("pdfjam $pages > \"work/$shortname.pdf\" ");

echo "$imax,work/".$shortname;
if($_POST['pdf'])
	echo ",pdf";
?>