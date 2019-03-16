<?php

/**
 * DEFAULT properties for pictures
 */

$pictures = [];


/**
 * Folder used to store uploaded pictures
 * - related to "global folder for dynamic content - aka old: $dynbasepath "
 */
$pictures['pcituresUploadFolder'] = "/images/upload";

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


