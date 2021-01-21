<?php
namespace src\Utils\FileSystem;


use src\Models\ChunkModels\UploadModel;
use RuntimeException;
use src\Utils\Generators\Uuid;

/**
 * This class is a generic handler for file uploading
 */
class FileUploadMgr
{
    /**
     * Handle upload of files based on description in upload model
     * If error occured files are not properly uploaded!
     *
     * @param UploadModel $model
     *
     * @throws \RuntimeException on error
     * @return string|string[] - file|array of files
     */
    public static function processFileUpload(UploadModel $model){

        self::checkUploadErrors($model);
        self::checkNumberOfFiles($model);
        self::checkFileSizes($model);
        self::checkFileMimeType($model);
        return self::saveUploadedFiles($model);
    }

    private static function checkDir($dir)
    {
        if(!file_exists($dir)){
            // no such dir - try to create one
            if(!mkdir($dir, 0755, true)){
                throw new RuntimeException("Can't save uploaded files - wrong path.");
            }
        }

        if(!is_dir($dir)){
            throw new RuntimeException("Can't save uploaded files - wrong dir.");
        }

        if(!is_writable($dir)){
            throw new RuntimeException("Can't save uploaded files - no permissions");
        }

    }

    /**
     * Save files to given destination.
     * Every file gets new name based on uuid + preserves old extension
     *
     * @param string $formVarName
     * @param string $uploadDir - dir where to store the files
     * @return string[] - array of filenames of files on server in given destination dir $this
     *                    + orginal filename as a key of each row
     */
    private static function saveUploadedFiles(UploadModel $model)
    {
        // first check the dir
        self::checkDir($model->getDirAtServer());

        $newFiles = [];
        foreach($_FILES[$model->formVarName]['name'] as $i => $name){

            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $fileName = Uuid::create() . ".$extension";
            $fullPath = $model->getDirAtServer() . '/' . $fileName;

            move_uploaded_file($_FILES[$model->formVarName]['tmp_name'][$i], $fullPath);

            $newFiles[$name] = $fileName;
        }
        return $newFiles;
    }

    /**
     * Compare MIME type of uploaded files with allowed mime types
     *
     * allowedMimeTypes can be:
     *  - one MIME value like: image/jpeg
     *  - generic MIME like: image/*
     *  - many types separated by pipe "|" like: image/*|text/*
     *
     * @param string $formVarName
     * @param string $allowedMimeTypes
     * @throws RuntimeException
     */
    private static function checkFileMimeType(UploadModel $model)
    {
        foreach($_FILES[$model->formVarName]['type'] as $type){
            if(!self::compareMimeType($type, $model->allowedTypesRegex)){
                throw new RuntimeException("Not allowed mime type: $type != {$model->allowedTypesRegex}");
            }
        }
    }

    /**
     * Compare given mime type to given mime types -
     * matches also generic mime types like image/* or text/*
     *
     * "Multipattern" is also acceptable: image/*|text/*
     *
     * @param string $type
     * @param string $pattern
     * @return boolean true if patern matches type
     */
    private static function compareMimeType($type, $pattern){
        $patterns = explode('|', $pattern);
        foreach ($patterns as $mime){
            $typeParts = explode('/', $type);
            $mimeParts = explode('/', $mime);

            // check the first partof mime: for examle "image" or "text"
            if($typeParts[0] != $mimeParts[0]){
                // not matched
                continue;
            }

            if($mimeParts[1] == '*'){
                // type matched!
                return true;
            }

            if($mimeParts[1] == $typeParts[1]){
                // second part matched = same mime type
                return true;
            }
        }
        return false; // pattern not matched
    }

    private static function checkNumberOfFiles(UploadModel $model)
    {
        $uploadedFiles = count($_FILES[$model->formVarName]['name']);
        if( $uploadedFiles > $model->maxFilesNumber ){
            throw new RuntimeException("Too many file uploaded at once: $uploadedFiles > {$model->maxFilesNumber}");
        }
    }

    /**
     * Check size of all uploaded files
     *
     * @param string $formVarName - upload var name
     * @param int $maxSize - max file size
     * @throws RuntimeException - if any file is too large
     */
    private static function checkFileSizes(UploadModel $model)
    {
        foreach($_FILES[$model->formVarName]['size'] as $size){
            if($size > $model->maxFileSize){
                throw new RuntimeException("Too large file uploaded: $size > {$model->maxFileSize}.");
            }
        }
    }

    /**
     * Check general upload errors
     *
     * @param string $formVarName
     * @throws RuntimeException
     */
    private static function checkUploadErrors(UploadModel $model)
    {
        $formVarName = $model->formVarName;

        if ( !isset($_FILES[$formVarName], $_FILES[$formVarName]['error'])) {
            /*
             * if upload fails here check error.log and php.ini for at least:
             *  - upload_max_filesize
             *  - post_max_size
             *  - memory_limit
             *  - file_uploads (should be On)
             */
            throw new RuntimeException('No files uploaded.');
        }

        // check all errors
        foreach($_FILES[$formVarName]['error'] as $error){

            switch ($error) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
                throw new RuntimeException('Exceeded server filesize limit.');
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded form filesize limit.');
            default:
                throw new RuntimeException('Unknown error.');
            }
        }
    }
}
