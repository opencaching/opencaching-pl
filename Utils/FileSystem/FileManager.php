<?php
namespace Utils\FileSystem;


class FileManager
{

    /**
     * Remove all files which matches $filePattern located in $dir older than $timeout
     *
     * @param $dir - directory to look up for files
     * @param $filePattern - pattern of filenames to remove
     * @param $timeout - time period in seconds
     */
    public static function removeFilesOlderThan($dir, $filePattern, $timeout){
        $files = glob($dir.$filePattern);
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $timeout) {
                    unlink($file);
                }
            }
        }
    }

}