<?php

  require('settings.inc.php');
  require('sql.inc.php');

  if (dbconnect() == false)
    die('cannot connect to database');



// read a file and return it as a string
// WARNING: no huge files!
function read_file($file='') 
{
  $fh = fopen($file, 'r');
  if ($fh)
  { 
    $content = fread($fh, filesize($file)); 
  }
		
  fclose($fh);

  return $content;
}

?>
