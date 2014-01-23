<?php
 /***************************************************************************
                                                    ./util/safemode_zip/phpzip.php
                                                            -------------------
        begin                : December 22 2005
        copyright            : (C) 2005 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

 /***************************************************************************

        Wrapper for unix-utilities unzip, gunzip and bunzip2


    ***************************************************************************/

$basedir = '/www/opencaching_www/www/download/zip/';

  $zipper['zip'] = 'nice --adjustment=19 unzip -qq -nj {src} -d {dst}';
  $zipper['gzip'] = 'nice --adjustment=19 gunzip -c -d -q {src} > {dst}';
  $zipper['bzip2'] = 'nice --adjustment=19 bunzip2 -c -d -q {src} > {dst}';

  if ($argv[1] == '--help')
    {
    echo $argv[0] . " --type=<ziptype> --src=<source> --dst=<destination>
--type   can be zip, gzip or bzip2
--src    relative* path to source file
--dst    relative* path to destination directory

*relative to $basedir
";
    exit;
  }

  if ((substr($argv[1], 0, 7) != '--type=') || (substr($argv[2], 0, 6) != '--src=') || (substr($argv[3], 0, 6) != '--dst='))
    die("wrong paramter\nuse " . $argv[0] . " --help\n");

  if (isset($argv[4]))
    die("wrong paramter\nuse " . $argv[0] . " --help\n");

  $type = substr($argv[1], 7);
  $src = substr($argv[2], 6);
  $dst = substr($argv[3], 6);

    if (!isset($zipper[$type]))
    die("invaild zip type\nuse " . $argv[0] . " --help\n");

  if (checkpath($src) == false)
    die("invaild src\nuse " . $argv[0] . " --help\n");

  if (checkpath($dst) == false)
    die("invaild dst\nuse " . $argv[0] . " --help\n");

    $src = $basedir . $src;
    $dst = $basedir . $dst;

  if (!file_exists($src))
    die("error: source file not exist\nuse " . $argv[0] . " --help\n");

  if (!is_dir($dst))
    die("error: destination directory not exist\nuse " . $argv[0] . " --help\n");

    if (($type == 'gzip') || ($type == 'bzip2'))
    {
        // zu dst-directory noch den dateinamen anhängen
        $filename = basename($src);
        if (strrpos($filename, '.') !== false)
            $filename = substr($filename, 0, strrpos($filename, '.'));

        $dst .= '/' . $filename;

        if (file_exists($dst))
        die("error: destination file exists\nuse " . $argv[0] . " --help\n");
  }

    $cmd = $zipper[$type];
    $cmd = str_replace('{src}', escapeshellcmd($src), $cmd);
    $cmd = str_replace('{dst}', escapeshellcmd($dst), $cmd);

    system($cmd);

function checkpath($path)
{
    $parts = explode('/', $path);

  if ($parts[0] == '')
        return false;

  for ($i = 0; $i < count($parts); $i++)
  {
    if (($parts[$i] == '..') || ($parts[$i] == '.'))
            return false;

        if (!preg_match('/^[a-zA-Z0-9.-_]{1,}/', $parts[$i]))
            return false;
  }

  return true;
}
?>
