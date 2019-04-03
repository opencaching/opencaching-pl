<?php
/***************************************************************************
 *  This tool currently fixes the following code style issues:
 *
 *    - resolve tabs to 4-char-columns
 *    - remove trailing whitespaces
 *    - set line ends to LF(-only)
 *    - remove ?> and blank lines at end of file
 *    - add missing LF to end of file
 *
 *  It also warns on the following issues:
 *
 *    - characters before open tag at start of file
 *    - short open tags
 *
 *  This script may be run any time to check and clean up the current OC code.
 *
 *  Original author: following
 *  License: MIT
 ***************************************************************************/

# The following code is made to run also on the developer host, which may
# have a restricted environent like an old Windows PHP. Keep it simple and
# do not include other OC code.

if (php_sapi_name() != "cli") {
    printf("This script should be run from command-line only.\n");
    exit(1);
}

$exclude = array(
    'lib/phpzip',
    'Libs',
    'mobile/lib/smarty',
    'okapi',
    'var',
    'vendor',
);

chdir(__DIR__ . '/../..');

$cleanup = new StyleCleanup();
$cleanup->run('.', $exclude);

echo
    $cleanup->getLinesModified() . ' lines in ' . $cleanup->getFilesModified() . ' files'
    . " have been cleaned up\n";


class StyleCleanup
{
    const TABWIDTH = 4;

    private $excludeDirs;
    private $basedir;
    private $filesModified;
    private $linesModified;

    public function run($basedir, $excludeDirs)
    {
        $this->basedir = $basedir;
        $this->excludeDirs = $excludeDirs;
        $this->filesModified = 0;
        $this->linesModified = 0;

        $this->cleanup($basedir);
    }

    public function getFilesModified()
    {
        return $this->filesModified;
    }

    public function getLinesModified()
    {
        return $this->linesModified;
    }

    private function cleanup($path)
    {
        if (!in_array(substr($path, strlen($this->basedir) + 1), $this->excludeDirs)) {

            # process files in $path

            $files = array_merge(
                glob($path . '/*.php'),
                glob($path . '/*.tpl')
            );

            foreach ($files as $filepath) {
                $fileModified = false;
                $lines = file($filepath);
                $displayFilepath = substr($filepath, strlen($this->basedir) + 1);

                # detect illegal characters at start of PHP or XML file

                if (count($lines) && preg_match('/^(.+?)\<\?( |=|php)/', $lines[0], $matches)) {
                    self::warn(
                        'invalid character(s) "' . $matches[1] . '" at start of ' . $displayFilepath
                    );
                }

                # Remove trailing whitespaces, strip CRs, expand tabs, make
                # sure that all - including the last - line end on "\n",
                # detect short open tags and bad character encodings.
                # Only-whitespace lines are allowed by PSR-2 and will not be trimmed.

                $n = 1;
                foreach ($lines as &$line) {
                    if ((trim($line, " \n") != '' || substr($line, -1) != "\n")
                        && !preg_match("/^ *(\\*|\/\/|#) *\n$/", $line)) {

                        $oldLine = $line;
                        $line = rtrim($line);
                        $line = $this->expandTabs($line);
                        $line .= "\n";

                        if ($line != $oldLine) {
                            $fileModified = true;
                            ++ $this->linesModified;
                        }

                        # This will detect all non-ASCII-non-UTF-8 except for C0..FF + A0..BF
                        # (Latin special letter + special char), which is extremely rare:

                        if (preg_match('/[\x{00}-\x{7F}][\x{80}-\x{BF}]|[\x{C0}-\x{FF}][^\x{80}-\x{BF}]/', $line)) {
                            self::warn("non-UTF8 encoding in $displayFilepath: $line");
                        }
                    }
                    if (preg_match('/\<\?\s/', $line)) {   # relies on \n at EOL
                        self::warn('short open tag in line ' . $n . ' of ' . $displayFilepath);
                    }
                    ++ $n;
                }
                unset($line);

                # fix PHPdoc comments

                $inPhpDoc = false;
                for ($l = 0; $l < count($lines); ++$l) {
                    $line = $lines[$l];
                    $trimmedLine = trim($line);

                    if (substr($trimmedLine, 0, 3) == '/**' && substr($trimmedLine, -2) != '*/') {
                        $inPhpDoc = true;
                    } elseif (substr($trimmedLine, -2) == '*/') {
                        $inPhpDoc = false;
                    } else if ($inPhpDoc && substr($trimmedLine, 0, 1) != '*') {
                        if (substr($line, 0, 2) == '   ') {
                            $line = ' * ' . substr($line, 3);
                        } else {
                            $line = ' * ' . $trimmedLine;
                        }
                        $lines[$l] = rtrim($line) . "\n";
                        $fileModified = true;
                    }
                }

                # remove PHP close tags and empty lines from end of file

                $l = count($lines) - 1;
                while ($l > 0) {
                    $trimmedLine = trim($lines[$l]);
                    if ($trimmedLine == '?>' || $trimmedLine == '') {
                        unset($lines[$l]);
                        $fileModified = true;
                        ++ $this->linesModified;
                    } else {
                        break;
                    }
                    -- $l;
                }

                if ($fileModified) {
                    echo 'cleaned ' . substr($filepath, 2) . "\n";
                    file_put_contents($filepath, implode('', $lines));
                    ++ $this->filesModified;
                }
            }

            # process subdirectories in $path

            $dirs = glob($path . '/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                if ($dir != '.' && $dir != '..') {
                    $this->cleanup($dir);
                }
            }
        }
    }

    private static function expandTabs($line)
    {
        while (($tabpos = strpos($line, "\t")) !== false) {
            $line =
                substr($line, 0, $tabpos)
                . substr('    ', 0, self::TABWIDTH - ($tabpos % self::TABWIDTH))
                . substr($line, $tabpos + 1);
        }

        return $line;
    }

    private static function warn($msg)
    {
        echo '! ' . $msg . "\n";
    }
}
