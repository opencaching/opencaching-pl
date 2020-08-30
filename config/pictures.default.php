<?php

/**
 * DEFAULT properties for pictures
 */
$pictures = [];

/**
 * Folder used to store uploaded pictures (former $picdir)
 * - related to "global folder for dynamic content - aka old: $dynbasepath "
 */
$pictures['picturesUploadFolder'] = "/images/uploads";

/**
 * Base of url to access pictures from browser (former $picurl)
 */
$pictures['picturesBaseUrl'] = "/images/uploads";

/**
 * Folder used to store thumbnails for uploaded pictures
 * - related to "global folder for dynamic content - aka old: $dynbasepath "
 */
$pictures['thumbnailFolder'] = "/images/upload/thumbnails";

/**
 * Max size of the thumbnails in px
 * array [x-y] or [width-height]
 */
$pictures['thumbnailSmall'] = [64, 64];
$pictures['thumbnailMedium'] = [175, 175];

/**
 * Max size (MB) of attached picture (this is internal only restriction)
 * Please note other additional http/php server side restrictions.
 */
$pictures['maxFileSize'] = 3.5; //$config['limits']['image']['filesize'] = 3.5;

/**
 * Do not resize images smaller than this size (MB)
 */
$pictures['resizeLargerThan'] = 0.1; // $config['limits']['image']['resize_larger'] = 0.2;

/**
 * Allowed picture extensions + its description
 */
$pictures['allowedExtensions'] = 'jpg,jpeg,gif,png';  // $config['limits']['image']['extension'] = ';jpg;jpeg;gif;png;';
$pictures['allowedExtensionsText'] = 'JPG, PNG, GIF'; // $config['limits']['image']['extension_text'] = 'JPG, PNG, GIF';


