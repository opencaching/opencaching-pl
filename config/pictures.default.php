<?php

/**
 * Pictures configuration
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 */

$pictures = [];

/**
 * Folder used to store uploaded pictures (former $picdir)
 * - related to "global folder for dynamic content - aka old: $dynbasepath "
 */
$pictures['picturesUploadFolder'] = '/images/uploads';

/**
 * Base of url to access pictures from browser (former $picurl)
 */
$pictures['picturesBaseUrl'] = '/images/uploads';

/**
 * Folder used to store thumbnails for uploaded pictures
 * - related to "global folder for dynamic content - aka old: $dynbasepath "
 */
$pictures['thumbnailFolder'] = '/images/upload/thumbnails';

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
$pictures['maxFileSize'] = 3.5; // former $config['limits']['image']['filesize']

/**
 * Do not resize images smaller than this size (MB)
 */
$pictures['resizeLargerThan'] = 0.2; // former $config['limits']['image']['resize_larger']

/**
 * Allowed picture extensions + its description
 */
$pictures['allowedExtensions'] = 'jpg,jpeg,gif,png';  // former $config['limits']['image']['extension']
$pictures['allowedExtensionsText'] = 'JPG, PNG, GIF'; // former $config['limits']['image']['extension_text'];
