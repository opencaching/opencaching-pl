<?php
namespace src\Utils\FileSystem;


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

    /**
     * Permanently remove given file.
     * This is alias to unlink.
     *
     * @param unknown $file
     */
    public static function removeFile($file)
    {
        unlink($file);
    }

    /**
     * Returns extension of the given file
     *
     * @param string $file  filename (can be with full path)
     */
    public static function getFileExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Returns the name of file without extensions (file can be with path)
     *
     * @param string $file
     * @return mixed
     */
    public static function getFileNameWithoutExtension($file)
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    /**
     * Returns filename with extensions from given path
     *
     * @param string $path  filename (with path)
     */
    public static function getFileNameWithExtension($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

}
